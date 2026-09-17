<?php

namespace App\Domain\Shared\Services;

use App\Domain\Shared\Models\OutboxEvent;
use App\Domain\Shared\ValueObjects\CanonicalId;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Event;
use Throwable;

class OutboxService
{
    /**
     * Record a new outbox event inside current database transaction.
     */
    public function record(
        string $eventName,
        string $aggregateType,
        string $aggregateId,
        string $organizationId,
        array $payload,
        ?string $branchId = null,
        ?string $actorId = null,
        string $actorType = 'staff',
        ?string $correlationId = null
    ): OutboxEvent {
        return OutboxEvent::create([
            'event_name' => $eventName,
            'aggregate_type' => $aggregateType,
            'aggregate_id' => $aggregateId,
            'organization_id' => $organizationId,
            'branch_id' => $branchId,
            'actor_id' => $actorId ?: 'system',
            'actor_type' => $actorType,
            'correlation_id' => $correlationId ?: CanonicalId::uuid7()->value(),
            'payload' => $payload,
            'status' => OutboxEvent::STATUS_PENDING,
        ]);
    }

    /**
     * Fetch pending outbox events ordered by creation time.
     *
     * @return Collection<int, OutboxEvent>
     */
    public function getPending(int $limit = 50): Collection
    {
        return OutboxEvent::where('status', OutboxEvent::STATUS_PENDING)
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Dispatch pending outbox events.
     *
     * @param callable|null $dispatcher Custom dispatch handler (defaults to Laravel Event::dispatch)
     * @return int Number of successfully dispatched events
     */
    public function dispatchPending(?callable $dispatcher = null, int $limit = 50): int
    {
        $events = $this->getPending($limit);
        $dispatchedCount = 0;

        foreach ($events as $event) {
            $event->update(['status' => OutboxEvent::STATUS_PROCESSING]);

            try {
                if ($dispatcher !== null) {
                    $dispatcher($event);
                } else {
                    Event::dispatch($event->event_name, [$event]);
                }

                $event->markDispatched();
                $dispatchedCount++;
            } catch (Throwable $e) {
                $event->markFailed($e->getMessage());
            }
        }

        return $dispatchedCount;
    }
}
