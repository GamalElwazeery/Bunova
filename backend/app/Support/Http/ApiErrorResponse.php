<?php

namespace App\Support\Http;

use App\Http\Middleware\CorrelationIdMiddleware;
use App\Support\Http\Exceptions\IdempotencyConcurrentConflictException;
use App\Support\Http\Exceptions\IdempotencyKeyRequiredException;
use App\Support\Http\Exceptions\IdempotencyPayloadMismatchException;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ApiErrorResponse
{
    /**
     * Build a standardized error JSON response.
     */
    public static function make(
        string $code,
        string $message,
        int $status = Response::HTTP_BAD_REQUEST,
        mixed $details = null,
        ?string $correlationId = null
    ): JsonResponse {
        $correlationId = $correlationId ?? self::resolveCorrelationId();

        $payload = [
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => $details,
                'correlation_id' => $correlationId,
                'timestamp' => Carbon::now()->toIso8601String(),
            ],
            'message' => $message,
        ];

        // Maintain standard Laravel compatibility for validation errors
        if ($details !== null && is_array($details) && $status === Response::HTTP_UNPROCESSABLE_ENTITY) {
            $payload['errors'] = $details;
        }

        $headers = [];
        if ($correlationId !== null) {
            $headers[CorrelationIdMiddleware::HEADER_NAME] = $correlationId;
            $payload['correlation_id'] = $correlationId;
        }

        return response()->json($payload, $status, $headers);
    }

    /**
     * Map any thrown exception to a standardized API error response.
     */
    public static function fromException(Throwable $e, Request $request): JsonResponse
    {
        $correlationId = $request->attributes->get('correlation_id')
            ?? $request->header(CorrelationIdMiddleware::HEADER_NAME)
            ?? $request->header(CorrelationIdMiddleware::REQUEST_ID_HEADER);

        if ($e instanceof ValidationException) {
            return self::make(
                code: 'VALIDATION_ERROR',
                message: $e->getMessage(),
                status: Response::HTTP_UNPROCESSABLE_ENTITY,
                details: $e->errors(),
                correlationId: $correlationId
            );
        }

        if ($e instanceof AuthenticationException) {
            return self::make(
                code: 'UNAUTHENTICATED',
                message: $e->getMessage() ?: 'Unauthenticated.',
                status: Response::HTTP_UNAUTHORIZED,
                correlationId: $correlationId
            );
        }

        if ($e instanceof AuthorizationException || $e instanceof AccessDeniedHttpException) {
            return self::make(
                code: 'FORBIDDEN',
                message: $e->getMessage() ?: 'This action is unauthorized.',
                status: Response::HTTP_FORBIDDEN,
                correlationId: $correlationId
            );
        }

        if ($e instanceof ModelNotFoundException || ($e instanceof NotFoundHttpException && $e->getPrevious() instanceof ModelNotFoundException)) {
            return self::make(
                code: 'RESOURCE_NOT_FOUND',
                message: 'The requested resource was not found.',
                status: Response::HTTP_NOT_FOUND,
                correlationId: $correlationId
            );
        }

        if ($e instanceof NotFoundHttpException) {
            return self::make(
                code: 'NOT_FOUND',
                message: $e->getMessage() ?: 'The requested endpoint was not found.',
                status: Response::HTTP_NOT_FOUND,
                correlationId: $correlationId
            );
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return self::make(
                code: 'METHOD_NOT_ALLOWED',
                message: $e->getMessage() ?: 'The HTTP method is not allowed for this endpoint.',
                status: Response::HTTP_METHOD_NOT_ALLOWED,
                correlationId: $correlationId
            );
        }

        if ($e instanceof IdempotencyKeyRequiredException) {
            return self::make(
                code: 'IDEMPOTENCY_KEY_REQUIRED',
                message: $e->getMessage(),
                status: Response::HTTP_BAD_REQUEST,
                correlationId: $correlationId
            );
        }

        if ($e instanceof IdempotencyPayloadMismatchException) {
            return self::make(
                code: 'IDEMPOTENCY_PAYLOAD_MISMATCH',
                message: $e->getMessage(),
                status: Response::HTTP_CONFLICT,
                correlationId: $correlationId
            );
        }

        if ($e instanceof IdempotencyConcurrentConflictException) {
            return self::make(
                code: 'IDEMPOTENCY_CONCURRENT_REQUEST',
                message: $e->getMessage(),
                status: Response::HTTP_CONFLICT,
                correlationId: $correlationId
            );
        }

        if ($e instanceof HttpExceptionInterface) {
            $statusCode = $e->getStatusCode();
            $code = match ($statusCode) {
                400 => 'BAD_REQUEST',
                401 => 'UNAUTHENTICATED',
                403 => 'FORBIDDEN',
                404 => 'NOT_FOUND',
                405 => 'METHOD_NOT_ALLOWED',
                409 => 'CONFLICT',
                422 => 'UNPROCESSABLE_ENTITY',
                429 => 'TOO_MANY_REQUESTS',
                503 => 'SERVICE_UNAVAILABLE',
                default => 'HTTP_ERROR_'.$statusCode,
            };

            return self::make(
                code: $code,
                message: $e->getMessage() ?: ('HTTP Error '.$statusCode),
                status: $statusCode,
                correlationId: $correlationId
            );
        }

        // Unhandled internal server error
        $isDebug = (bool) config('app.debug', false);
        $message = $isDebug ? $e->getMessage() : 'An unexpected server error occurred.';
        $details = $isDebug ? [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ] : null;

        return self::make(
            code: 'INTERNAL_SERVER_ERROR',
            message: $message,
            status: Response::HTTP_INTERNAL_SERVER_ERROR,
            details: $details,
            correlationId: $correlationId
        );
    }

    /**
     * Resolve correlation ID from active request.
     */
    protected static function resolveCorrelationId(): ?string
    {
        /** @var Request|null $request */
        $request = request();
        if (!$request) {
            return null;
        }

        return $request->attributes->get('correlation_id')
            ?? $request->header(CorrelationIdMiddleware::HEADER_NAME)
            ?? $request->header(CorrelationIdMiddleware::REQUEST_ID_HEADER);
    }
}
