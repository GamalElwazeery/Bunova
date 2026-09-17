<?php

namespace App\Domain\Shared\Jobs;

use App\Domain\Shared\Services\OutboxService;
use App\Support\Jobs\DomainJob;

class ProcessOutboxJob extends DomainJob
{
    public function __construct(
        public int $batchSize = 50,
        ?string $correlationId = null
    ) {
        parent::__construct(correlationId: $correlationId);
    }

    /**
     * Execute the job to dispatch pending outbox events.
     */
    public function handle(OutboxService $outboxService): int
    {
        $this->restoreContext();

        return $outboxService->dispatchPending(limit: $this->batchSize);
    }
}
