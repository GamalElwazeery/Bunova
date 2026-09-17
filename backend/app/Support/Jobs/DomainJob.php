<?php

namespace App\Support\Jobs;

use App\Domain\Identity\TenantContext;
use App\Http\Middleware\CorrelationIdMiddleware;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

abstract class DomainJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public ?string $correlationId;
    public ?string $organizationId;
    public ?string $branchId;
    public ?string $idempotencyKey;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 5;

    public function __construct(
        ?string $organizationId = null,
        ?string $branchId = null,
        ?string $correlationId = null,
        ?string $idempotencyKey = null
    ) {
        $this->organizationId = $organizationId ?? TenantContext::getOrganizationId();
        $this->branchId = $branchId ?? TenantContext::getBranchId();

        if ($correlationId !== null) {
            $this->correlationId = $correlationId;
        } else {
            $req = request();
            $this->correlationId = $req?->attributes->get('correlation_id')
                ?? $req?->header(CorrelationIdMiddleware::HEADER_NAME)
                ?? (string) Str::uuid7();
        }

        $this->idempotencyKey = $idempotencyKey;
    }

    /**
     * Restore contextual tenant and tracing information into the job execution environment.
     */
    protected function restoreContext(): void
    {
        if ($this->correlationId !== null) {
            Log::withContext([
                'correlation_id' => $this->correlationId,
            ]);
        }

        if ($this->organizationId !== null) {
            TenantContext::setOrganization($this->organizationId);
        }

        if ($this->branchId !== null) {
            TenantContext::setBranch($this->branchId);
        }
    }
}
