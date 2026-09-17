<?php

namespace App\Support\Observability;

use App\Domain\Identity\TenantContext;
use App\Domain\Shared\ValueObjects\AuditContext;
use App\Http\Middleware\CorrelationIdMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ErrorTracker
{
    /**
     * Centralized exception capture with correlation tracking and automated PII redaction.
     */
    public function captureException(Throwable $e, array $additionalContext = []): string
    {
        $errorEventId = (string) Str::uuid7();
        $req = request() instanceof Request ? request() : null;

        $correlationId = $additionalContext['correlation_id']
            ?? $req?->attributes->get('correlation_id')
            ?? $req?->header(CorrelationIdMiddleware::HEADER_NAME)
            ?? (string) Str::uuid7();

        $cleanContext = AuditContext::redactSensitiveData($additionalContext);

        $telemetry = [
            'error_event_id' => $errorEventId,
            'correlation_id' => $correlationId,
            'exception' => [
                'class' => get_class($e),
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ],
            'tenant' => [
                'organization_id' => TenantContext::getOrganizationId(),
                'branch_id' => TenantContext::getBranchId(),
            ],
            'request' => $req ? [
                'method' => $req->method(),
                'path' => $req->path(),
                'ip' => $req->ip(),
            ] : null,
            'context' => $cleanContext,
        ];

        Log::error("Unhandled exception: {$e->getMessage()}", $telemetry);

        return $errorEventId;
    }
}
