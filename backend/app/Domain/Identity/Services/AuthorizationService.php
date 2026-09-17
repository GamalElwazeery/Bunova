<?php

namespace App\Domain\Identity\Services;

use App\Domain\Identity\Models\Branch;
use App\Domain\Identity\Models\StaffIdentity;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class AuthorizationService
{
    /**
     * Check if a StaffIdentity has permission to perform an action within a branch/org scope.
     */
    public function canStaff(StaffIdentity $staff, string $permission, ?string $branchId = null): bool
    {
        // 1. Inactive or suspended staff cannot perform any action
        if (!$staff->isActive()) {
            return false;
        }

        // 2. Operational staff can NEVER acquire or execute platform operator privileges
        if (str_starts_with($permission, 'platform.')) {
            return false;
        }

        // 3. If a branch is specified, verify it belongs to the staff's organization
        if ($branchId !== null) {
            $branch = Branch::where('id', $branchId)->first();
            if (!$branch || $branch->organization_id !== $staff->organization_id) {
                return false;
            }
        }

        // 4. Delegate to StaffIdentity role & branch assignment resolution
        return $staff->hasPermission($permission, $branchId);
    }

    /**
     * Check if a User has permission to perform an action.
     */
    public function canUser(User $user, string $permission, ?string $organizationId = null): bool
    {
        // 1. Inactive or suspended users cannot perform any action
        if (!$user->isActive()) {
            return false;
        }

        // 2. Platform permissions require platform operator type
        if (str_starts_with($permission, 'platform.')) {
            return $user->isPlatformOperator();
        }

        // 3. Platform operators cannot silently execute tenant operational actions without context
        if ($user->isPlatformOperator()) {
            return false;
        }

        // 4. If organization is specified, verify tenant user belongs to it
        if ($organizationId !== null && $user->organization_id !== $organizationId) {
            return false;
        }

        // 5. If user has an associated staff identity, check operational staff permissions
        if ($user->staffIdentity) {
            return $this->canStaff($user->staffIdentity, $permission);
        }

        return false;
    }

    /**
     * Assert that a staff member has permission, or throw AuthorizationException.
     *
     * @throws AuthorizationException
     */
    public function authorizeStaff(StaffIdentity $staff, string $permission, ?string $branchId = null): void
    {
        if (!$this->canStaff($staff, $permission, $branchId)) {
            throw new AuthorizationException(
                "Staff member '{$staff->display_name}' is not authorized to execute '{$permission}'" .
                ($branchId ? " at branch '{$branchId}'" : '') . '.'
            );
        }
    }

    /**
     * Assert that a user has permission, or throw AuthorizationException.
     *
     * @throws AuthorizationException
     */
    public function authorizeUser(User $user, string $permission, ?string $organizationId = null): void
    {
        if (!$this->canUser($user, $permission, $organizationId)) {
            throw new AuthorizationException(
                "User '{$user->name}' is not authorized to execute '{$permission}'" .
                ($organizationId ? " in organization '{$organizationId}'" : '') . '.'
            );
        }
    }
}
