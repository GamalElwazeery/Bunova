<?php

namespace App\Domain\Shared\Traits;

use App\Domain\Shared\Models\AuditEvent;
use App\Domain\Shared\Services\AuditService;

trait RecordsAuditEvents
{
    /**
     * Record a sensitive domain action in the canonical audit trail.
     */
    protected function recordAudit(
        string $organizationId,
        string $actionKey,
        string $targetType,
        string $targetId,
        ?string $reason = null,
        array $before = [],
        array $after = [],
        ?string $branchId = null,
        ?string $actorId = null,
        ?string $actorType = null,
        ?string $deviceId = null,
        ?string $correlationId = null
    ): AuditEvent {
        /** @var AuditService $service */
        $service = app(AuditService::class);

        return $service->recordSensitiveAction(
            organizationId: $organizationId,
            actionKey: $actionKey,
            targetType: $targetType,
            targetId: $targetId,
            reason: $reason,
            before: $before,
            after: $after,
            branchId: $branchId,
            actorId: $actorId,
            actorType: $actorType,
            deviceId: $deviceId,
            correlationId: $correlationId
        );
    }
}
