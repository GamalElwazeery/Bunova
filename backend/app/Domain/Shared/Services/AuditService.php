<?php

namespace App\Domain\Shared\Services;

use App\Domain\Identity\TenantContext;
use App\Domain\Shared\Models\AuditEvent;
use App\Domain\Shared\ValueObjects\AuditContext;
use App\Http\Middleware\CorrelationIdMiddleware;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuditService
{
    /**
     * Record an immutable audit event from AuditContext.
     */
    public function record(AuditContext $context): AuditEvent
    {
        $data = $context->jsonSerialize();

        $event = AuditEvent::create([
            'id' => $data['event_id'],
            'organization_id' => $data['organization_id'],
            'branch_id' => $data['branch_id'],
            'occurred_at' => Carbon::parse($data['occurred_at']),
            'actor_id' => $data['actor_id'],
            'actor_type' => $data['actor_type'],
            'device_id' => $data['device_id'],
            'action_key' => $data['action_key'],
            'target_type' => $data['target_type'],
            'target_id' => $data['target_id'],
            'correlation_id' => $data['correlation_id'],
            'reason' => $data['reason'],
            'payload' => $data['payload'],
            'ip_address' => $data['ip_address'],
            'user_agent' => $data['user_agent'],
            'created_at' => Carbon::now('UTC'),
        ]);

        Log::info('Audit event recorded: '.$event->action_key, [
            'audit_event_id' => $event->id,
            'organization_id' => $event->organization_id,
            'branch_id' => $event->branch_id,
            'action_key' => $event->action_key,
            'target_type' => $event->target_type,
            'target_id' => $event->target_id,
            'correlation_id' => $event->correlation_id,
        ]);

        return $event;
    }

    /**
     * Helper to record sensitive operational actions with automatic request context extraction and diffing.
     */
    public function recordSensitiveAction(
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
        ?string $correlationId = null,
        ?Request $request = null
    ): AuditEvent {
        $req = $request ?? (request() instanceof Request ? request() : null);

        // Resolve branch context
        $branchId = $branchId
            ?? TenantContext::getBranch()
            ?? $req?->header('X-Branch-ID');

        // Resolve actor context
        if ($actorId === null) {
            if ($staff = $req?->attributes->get('staff')) {
                $actorId = $staff->id;
                $actorType = AuditContext::ACTOR_STAFF;
            } elseif ($user = $req?->user()) {
                $actorId = $user->id;
                $actorType = AuditContext::ACTOR_USER;
            } elseif ($device = $req?->attributes->get('device')) {
                $actorId = $device->id;
                $actorType = AuditContext::ACTOR_DEVICE;
                $deviceId = $device->id;
            } else {
                $actorId = 'system';
                $actorType = AuditContext::ACTOR_SYSTEM;
            }
        } else {
            $actorType = $actorType ?? AuditContext::ACTOR_STAFF;
        }

        // Resolve device context
        $deviceId = $deviceId
            ?? $req?->attributes->get('device')?->id
            ?? $req?->header('X-Device-ID');

        // Resolve correlation ID
        $correlationId = $correlationId
            ?? $req?->attributes->get('correlation_id')
            ?? $req?->header(CorrelationIdMiddleware::HEADER_NAME)
            ?? $req?->header(CorrelationIdMiddleware::REQUEST_ID_HEADER);

        // Compute before/after diff safely
        $cleanBefore = AuditContext::redactSensitiveData($before);
        $cleanAfter = AuditContext::redactSensitiveData($after);
        $diff = $this->computeDiff($cleanBefore, $cleanAfter);

        $payload = [
            'before' => $cleanBefore,
            'after' => $cleanAfter,
            'changed_keys' => array_keys($diff),
            'diff' => $diff,
        ];

        $context = AuditContext::create(
            organizationId: $organizationId,
            actionKey: $actionKey,
            targetType: $targetType,
            targetId: $targetId,
            actorId: $actorId,
            actorType: $actorType,
            branchId: $branchId,
            deviceId: $deviceId,
            correlationId: $correlationId,
            reason: $reason,
            payload: $payload,
            ipAddress: $req?->ip(),
            userAgent: $req?->userAgent()
        );

        return $this->record($context);
    }

    /**
     * Query audit events for an organization with filtering.
     */
    public function query(string $organizationId, array $filters = []): Builder
    {
        $query = AuditEvent::forOrganization($organizationId)->recentFirst();

        if (!empty($filters['branch_id'])) {
            $query->forBranch($filters['branch_id']);
        }

        if (!empty($filters['action_key'])) {
            $query->forAction($filters['action_key']);
        }

        if (!empty($filters['actor_id'])) {
            $query->forActor($filters['actor_id']);
        }

        if (!empty($filters['target_type']) && !empty($filters['target_id'])) {
            $query->forTarget($filters['target_type'], $filters['target_id']);
        }

        if (!empty($filters['start_date'])) {
            $query->where('occurred_at', '>=', Carbon::parse($filters['start_date']));
        }

        if (!empty($filters['end_date'])) {
            $query->where('occurred_at', '<=', Carbon::parse($filters['end_date']));
        }

        return $query;
    }

    /**
     * Compute array diff representing changed fields.
     */
    protected function computeDiff(array $before, array $after): array
    {
        $diff = [];
        $allKeys = array_unique(array_merge(array_keys($before), array_keys($after)));

        foreach ($allKeys as $key) {
            $valBefore = $before[$key] ?? null;
            $valAfter = $after[$key] ?? null;

            if ($valBefore !== $valAfter) {
                $diff[$key] = [
                    'from' => $valBefore,
                    'to' => $valAfter,
                ];
            }
        }

        return $diff;
    }
}
