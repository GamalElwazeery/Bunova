<?php

namespace App\Domain\Identity\Scopes;

use App\Domain\Identity\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OrganizationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (TenantContext::hasOrganization()) {
            $builder->where($model->getTable() . '.organization_id', TenantContext::getOrganizationId());
        }
    }
}
