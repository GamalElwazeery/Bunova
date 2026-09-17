<?php

namespace App\Domain\Billing\Policies;

use App\Domain\Identity\Models\StaffIdentity;
use App\Domain\Identity\Services\AuthorizationService;
use App\Domain\Identity\Support\PermissionCatalog;
use App\Models\User;

class BillPolicy
{
    public function __construct(
        protected AuthorizationService $authService
    ) {}

    public function view(StaffIdentity|User $actor, ?string $branchId = null): bool
    {
        return $this->check($actor, PermissionCatalog::BILLS_VIEW, $branchId);
    }

    public function create(StaffIdentity|User $actor, ?string $branchId = null): bool
    {
        return $this->check($actor, PermissionCatalog::BILLS_CREATE, $branchId);
    }

    public function finalize(StaffIdentity|User $actor, ?string $branchId = null): bool
    {
        return $this->check($actor, PermissionCatalog::BILLS_FINALIZE, $branchId);
    }

    public function void(StaffIdentity|User $actor, ?string $branchId = null): bool
    {
        return $this->check($actor, PermissionCatalog::BILLS_VOID, $branchId);
    }

    public function applyDiscount(StaffIdentity|User $actor, ?string $branchId = null): bool
    {
        return $this->check($actor, PermissionCatalog::BILLS_DISCOUNT, $branchId);
    }

    protected function check(StaffIdentity|User $actor, string $permission, ?string $branchId): bool
    {
        if ($actor instanceof StaffIdentity) {
            return $this->authService->canStaff($actor, $permission, $branchId);
        }

        return $this->authService->canUser($actor, $permission);
    }
}
