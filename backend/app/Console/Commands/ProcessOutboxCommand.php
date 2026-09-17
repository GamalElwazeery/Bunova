<?php

namespace App\Console\Commands;

use App\Domain\Shared\Services\OutboxService;
use Illuminate\Console\Command;

class ProcessOutboxCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'outbox:process {--limit=50 : Maximum number of events to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process and dispatch pending transactional outbox events';

    /**
     * Execute the console command.
     */
    public function handle(OutboxService $outboxService): int
    {
        $limit = (int) $this->option('limit');
        $this->info("Processing pending outbox events (limit: {$limit})...");

        $dispatched = $outboxService->dispatchPending(limit: $limit);

        $this->info("Successfully dispatched {$dispatched} outbox events.");

        return Command::SUCCESS;
    }
}
