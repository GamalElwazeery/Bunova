<?php

namespace App\Console\Commands;

use App\Domain\Shared\Services\InboxService;
use Illuminate\Console\Command;

class PruneInboxCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inbox:prune {--days=30 : Prune messages processed older than this many days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune old processed inbox messages';

    /**
     * Execute the console command.
     */
    public function handle(InboxService $inboxService): int
    {
        $days = (int) $this->option('days');
        $this->info("Pruning processed inbox messages older than {$days} days...");

        $deleted = $inboxService->prune($days);

        $this->info("Successfully pruned {$deleted} old inbox messages.");

        return Command::SUCCESS;
    }
}
