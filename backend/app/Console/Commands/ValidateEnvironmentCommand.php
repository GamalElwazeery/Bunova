<?php

namespace App\Console\Commands;

use App\Support\Config\EnvironmentValidator;
use Illuminate\Console\Command;

class ValidateEnvironmentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:validate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate environment configuration and secret safety across local/test/staging/production.';

    /**
     * Execute the console command.
     */
    public function handle(EnvironmentValidator $validator): int
    {
        $this->info('Validating Bunova environment configuration...');
        $this->line('Environment: ' . config('app.env'));

        $result = $validator->validate();

        foreach ($result['warnings'] as $warning) {
            $this->warn("  [WARN] {$warning}");
        }

        if (!$result['valid']) {
            foreach ($result['errors'] as $error) {
                $this->error("  [FAIL] {$error}");
            }
            $this->error('Environment validation failed with ' . count($result['errors']) . ' error(s).');
            return Command::FAILURE;
        }

        $this->info('Environment validation passed. Configuration is valid and safe.');
        return Command::SUCCESS;
    }
}
