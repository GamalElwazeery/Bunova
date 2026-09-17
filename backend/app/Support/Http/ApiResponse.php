<?php

namespace App\Support\Http;

use App\Http\Middleware\CorrelationIdMiddleware;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    /**
     * Return a standardized success JSON response.
     */
    public static function success(
        mixed $data = null,
        ?string $message = null,
        int $status = Response::HTTP_OK,
        array $meta = []
    ): JsonResponse {
        $payload = [
            'success' => true,
        ];

        if ($message !== null) {
            $payload['message'] = $message;
        }

        if ($data !== null) {
            $payload['data'] = $data;
        }

        if (!empty($meta)) {
            $payload['meta'] = $meta;
        }

        $correlationId = self::getCorrelationId();
        if ($correlationId !== null) {
            $payload['correlation_id'] = $correlationId;
        }

        return response()->json($payload, $status);
    }

    /**
     * Return a standardized paginated response.
     */
    public static function paginated(
        LengthAwarePaginator|CursorPaginator $paginator,
        ?string $message = null,
        int $status = Response::HTTP_OK,
        array $extraMeta = []
    ): JsonResponse {
        $paginationMeta = [];

        if ($paginator instanceof LengthAwarePaginator) {
            $paginationMeta = [
                'type' => 'page',
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'total_pages' => $paginator->lastPage(),
                'has_more' => $paginator->hasMorePages(),
            ];
        } elseif ($paginator instanceof CursorPaginator) {
            $paginationMeta = [
                'type' => 'cursor',
                'per_page' => $paginator->perPage(),
                'has_more' => $paginator->hasMorePages(),
                'next_cursor' => $paginator->nextCursor()?->encode(),
                'prev_cursor' => $paginator->previousCursor()?->encode(),
            ];
        }

        $meta = array_merge(['pagination' => $paginationMeta], $extraMeta);

        return self::success(
            data: $paginator->items(),
            message: $message,
            status: $status,
            meta: $meta
        );
    }

    /**
     * Return a 204 No Content response.
     */
    public static function noContent(): Response
    {
        return response()->noContent();
    }

    /**
     * Get the active correlation ID from current request if available.
     */
    protected static function getCorrelationId(): ?string
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
