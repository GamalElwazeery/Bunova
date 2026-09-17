<?php

namespace App\Support\Logging;

use App\Domain\Identity\TenantContext;
use App\Domain\Shared\ValueObjects\AuditContext;
use App\Http\Middleware\CorrelationIdMiddleware;
use Carbon\Carbon;
use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;

class StructuredJsonFormatter implements FormatterInterface
{
    /**
     * Format a Monolog LogRecord into a standardized structured JSON payload with secret redaction.
     */
    public function format(LogRecord $record): string
    {
        $context = $record->context;

        // Automatically redact passwords, tokens, PINs, secrets from context
        $cleanContext = AuditContext::redactSensitiveData($context);

        $correlationId = $cleanContext['correlation_id']
            ?? (request() ? request()->attributes->get('correlation_id') : null)
            ?? (request() ? request()->header(CorrelationIdMiddleware::HEADER_NAME) : null);

        $payload = [
            'timestamp' => $record->datetime->format(Carbon::ATOM),
            'level' => $record->level->getName(),
            'message' => $record->message,
            'channel' => $record->channel,
            'correlation_id' => $correlationId,
            'environment' => config('app.env', 'production'),
            'tenant' => [
                'organization_id' => TenantContext::getOrganizationId(),
                'branch_id' => TenantContext::getBranchId(),
            ],
            'context' => $cleanContext,
        ];

        // If an exception was logged, include sanitized exception information
        if (isset($record->context['exception']) && $record->context['exception'] instanceof \Throwable) {
            $e = $record->context['exception'];
            $payload['exception'] = [
                'class' => get_class($e),
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
            unset($payload['context']['exception']);
        }

        return json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
    }

    /**
     * Format a set of log records.
     */
    public function formatBatch(array $records): string
    {
        $message = '';
        foreach ($records as $record) {
            $message .= $this->format($record);
        }

        return $message;
    }
}
