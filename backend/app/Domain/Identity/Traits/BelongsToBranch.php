<?php

namespace App\Domain\Identity\Traits;

use App\Domain\Identity\Models\Branch;
use App\Domain\Identity\Scopes\BranchScope;
use App\Domain\Identity\TenantContext;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToBranch
{
    /**
     * Boot the trait and apply the global branch scope and default assignment.
     */
    public static function bootBelongsToBranch(): void
    {
        static::addGlobalScope(new BranchScope());

        static::creating(function ($model) {
            if (empty($model->branch_id) && TenantContext::hasBranch()) {
                $model->branch_id = TenantContext::getBranchId();
            }
        });
    }

    /**
     * Relationship to the parent Branch.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
