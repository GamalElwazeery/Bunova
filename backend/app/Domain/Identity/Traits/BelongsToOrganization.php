<?php

namespace App\Domain\Identity\Traits;

use App\Domain\Identity\Models\Organization;
use App\Domain\Identity\Scopes\OrganizationScope;
use App\Domain\Identity\TenantContext;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToOrganization
{
    /**
     * Boot the trait and apply the global organization scope and default assignment.
     */
    public static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope(new OrganizationScope());

        static::creating(function ($model) {
            if (empty($model->organization_id) && TenantContext::hasOrganization()) {
                $model->organization_id = TenantContext::getOrganizationId();
            }
        });
    }

    /**
     * Relationship to the parent Organization.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }
}
