<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CorrelationIdMiddleware
{
    public const HEADER_NAME = 'X-Correlation-ID';
    public const REQUEST_ID_HEADER = 'X-Request-ID';

    /**
     * Handle an incoming request and ensure correlation ID is tracked end-to-end.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $correlationId = $this->resolveCorrelationId($request);

        // Store correlation ID in request attributes
        $request->attributes->set('correlation_id', $correlationId);

        // Bind into Laravel structured logging context
        Log::withContext([
            'correlation_id' => $correlationId,
        ]);

        /** @var Response $response */
        $response = $next($request);

        // Ensure header is propagated back to caller
        $response->headers->set(self::HEADER_NAME, $correlationId);
        $response->headers->set('X-Bunova-Version', 'v1');

        return $response;
    }

    /**
     * Resolve incoming correlation ID or generate a new UUIDv7.
     */
    protected function resolveCorrelationId(Request $request): string
    {
        $candidate = $request->header(self::HEADER_NAME) ?? $request->header(self::REQUEST_ID_HEADER);

        if (is_string($candidate) && $candidate !== '' && strlen($candidate) <= 64) {
            // Validate safe characters: alphanumeric, dash, underscore, dot
            if (preg_match('/^[a-zA-Z0-9\-_.]+$/', $candidate)) {
                return $candidate;
            }
        }

        return (string) Str::uuid7();
    }
}
