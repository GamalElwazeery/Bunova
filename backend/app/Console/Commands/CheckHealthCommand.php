<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CheckHealthCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'health:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inspect connectivity and health for database, cache, redis, and storage dependencies.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking Bunova local dependencies and service health...');
        $hasErrors = false;

        // 1. Database
        try {
            DB::connection()->getPdo();
            $driver = DB::connection()->getDriverName();
            $database = config("database.connections.{$driver}.database");
            $this->info("  [PASS] Database ({$driver}): connected to [{$database}]");
        } catch (Exception $e) {
            $hasErrors = true;
            $this->error("  [FAIL] Database connection failed: {$e->getMessage()}");
        }

        // 2. Cache / Redis
        try {
            $store = config('cache.default');
            $testKey = 'health_cli_probe_' . time();
            Cache::put($testKey, 'ok', 5);
            $cached = Cache::get($testKey);
            Cache::forget($testKey);

            if ($cached === 'ok') {
                $this->info("  [PASS] Cache ({$store}): read/write verified");
            } else {
                $hasErrors = true;
                $this->error("  [FAIL] Cache ({$store}): value did not match");
            }
        } catch (Exception $e) {
            $hasErrors = true;
            $this->error("  [FAIL] Cache/Redis failed: {$e->getMessage()}");
        }

        // 3. Storage
        try {
            $disk = config('filesystems.default');
            $testFile = 'health_cli_probe.txt';
            Storage::disk($disk)->put($testFile, 'probe');
            Storage::disk($disk)->delete($testFile);
            $this->info("  [PASS] Storage ({$disk}): write/delete verified");
        } catch (Exception $e) {
            $hasErrors = true;
            $this->error("  [FAIL] Storage disk failed: {$e->getMessage()}");
        }

        if ($hasErrors) {
            $this->error('Health check finished with errors.');
            return Command::FAILURE;
        }

        $this->info('All services healthy.');
        return Command::SUCCESS;
    }
}
