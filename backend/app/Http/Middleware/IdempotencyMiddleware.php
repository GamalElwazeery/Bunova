<?php

namespace App\Http\Middleware;

use App\Support\Http\Exceptions\IdempotencyKeyRequiredException;
use App\Support\Http\IdempotencyService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class IdempotencyMiddleware
{
    public const HEADER_NAME = 'Idempotency-Key';
    public const ALT_HEADER_NAME = 'X-Idempotency-Key';

    public function __construct(
        protected IdempotencyService $idempotencyService
    ) {}

    /**
     * Handle an incoming request with idempotency protection.
     */
    public function handle(Request $request, Closure $next, string $mode = 'required'): Response
    {
        $key = $request->header(self::HEADER_NAME) ?? $request->header(self::ALT_HEADER_NAME);

        if ($key === null || trim($key) === '') {
            if ($mode === 'required') {
                throw new IdempotencyKeyRequiredException();
            }

            return $next($request);
        }

        $key = trim($key);
        if (strlen($key) > 255 || !preg_match('/^[a-zA-Z0-9\-_:.]+$/', $key)) {
            throw new HttpException(400, 'Invalid Idempotency-Key format. Must be 1-255 alphanumeric, dash, underscore, colon, or dot.');
        }

        $requestHash = $this->idempotencyService->computeRequestHash($request);
        $scope = $this->idempotencyService->resolveScope($request);

        $record = $this->idempotencyService->claimLock($scope, $key, $requestHash);

        // If this was an existing completed record, replay the cached response
        if ($record->isCompleted()) {
            $headers = $record->response_headers ?? [];
            $headers['X-Idempotent-Replay'] = 'true';
            if (!isset($headers[CorrelationIdMiddleware::HEADER_NAME])) {
                $headers[CorrelationIdMiddleware::HEADER_NAME] = $request->attributes->get('correlation_id');
            }

            return response()->json(
                $record->response_body,
                $record->response_code ?? Response::HTTP_OK,
                $headers
            );
        }

        try {
            /** @var Response $response */
            $response = $next($request);

            if ($response->getStatusCode() >= 500) {
                $this->idempotencyService->markFailed($record);
            } else {
                $body = null;
                if ($response instanceof JsonResponse) {
                    $body = $response->getData(true);
                } else {
                    $raw = $response->getContent();
                    $decoded = json_decode((string) $raw, true);
                    $body = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $raw;
                }

                $headers = [];
                foreach ($response->headers->all() as $name => $values) {
                    $headers[$name] = implode(', ', $values);
                }

                $this->idempotencyService->markCompleted(
                    $record,
                    $response->getStatusCode(),
                    $headers,
                    $body
                );
            }

            return $response;
        } catch (\Throwable $e) {
            $this->idempotencyService->markFailed($record);
            throw $e;
        }
    }
}
