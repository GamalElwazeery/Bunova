<?php

namespace App\Domain\Orders\Policies;

use App\Domain\Identity\Models\StaffIdentity;
use App\Domain\Identity\Services\AuthorizationService;
use App\Domain\Identity\Support\PermissionCatalog;
use App\Models\User;

class OrderPolicy
{
    public function __construct(
        protected AuthorizationService $authService
    ) {}

    public function view(StaffIdentity|User $actor, ?string $branchId = null): bool
    {
        return $this->check($actor, PermissionCatalog::ORDERS_VIEW, $branchId);
    }

    public function create(StaffIdentity|User $actor, ?string $branchId = null): bool
    {
        return $this->check($actor, PermissionCatalog::ORDERS_CREATE, $branchId);
    }

    public function update(StaffIdentity|User $actor, ?string $branchId = null): bool
    {
        return $this->check($actor, PermissionCatalog::ORDERS_UPDATE, $branchId);
    }

    public function cancel(StaffIdentity|User $actor, ?string $branchId = null): bool
    {
        return $this->check($actor, PermissionCatalog::ORDERS_CANCEL, $branchId);
    }

    public function applyDiscount(StaffIdentity|User $actor, ?string $branchId = null): bool
    {
        return $this->check($actor, PermissionCatalog::ORDERS_DISCOUNT, $branchId);
    }

    public function voidItem(StaffIdentity|User $actor, ?string $branchId = null): bool
    {
        return $this->check($actor, PermissionCatalog::ORDERS_VOID_ITEM, $branchId);
    }

    protected function check(StaffIdentity|User $actor, string $permission, ?string $branchId): bool
    {
        if ($actor instanceof StaffIdentity) {
            return $this->authService->canStaff($actor, $permission, $branchId);
        }

        return $this->authService->canUser($actor, $permission);
    }
}
