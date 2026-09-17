<?php

namespace App\Domain\Identity\Services;

use App\Domain\Identity\Models\Branch;
use App\Domain\Identity\Models\Organization;
use App\Domain\Identity\Models\StaffIdentity;
use App\Domain\Identity\TenantContext;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationService
{
    /**
     * Authenticate a web or cloud user via email and password.
     *
     * @throws AuthenticationException
     */
    public function authenticateUser(string $email, string $password): User
    {
        /** @var User|null $user */
        $user = TenantContext::withoutTenancy(function () use ($email) {
            return User::where('email', $email)->first();
        });

        if (!$user || !Hash::check($password, $user->password)) {
            throw new AuthenticationException('Invalid user credentials.');
        }

        if (!$user->isActive()) {
            throw new AuthenticationException('User account is suspended or inactive.');
        }

        // If tenant user, verify organization status
        if ($user->isTenantUser() && $user->organization_id) {
            $org = Organization::find($user->organization_id);
            if (!$org || !$org->isActive()) {
                throw new AuthenticationException('Organization account is inactive or suspended.');
            }
        }

        return $user;
    }

    /**
     * Authenticate an operational staff member via staff code and PIN for a specific branch.
     *
     * @throws AuthenticationException
     */
    public function authenticateStaff(string $organizationId, string $branchId, string $staffCode, string $pin): StaffIdentity
    {
        /** @var StaffIdentity|null $staff */
        $staff = TenantContext::withoutTenancy(function () use ($organizationId, $staffCode) {
            return StaffIdentity::where('organization_id', $organizationId)
                ->where('staff_code', $staffCode)
                ->first();
        });

        if (!$staff || !$staff->verifyPin($pin)) {
            throw new AuthenticationException('Invalid staff identifier or PIN.');
        }

        if (!$staff->isActive()) {
            throw new AuthenticationException('Staff member is suspended or inactive.');
        }

        if (!$staff->isAssignedToBranch($branchId)) {
            throw new AuthenticationException('Staff member is not authorized for this branch.');
        }

        return $staff;
    }

    /**
     * Authenticate staff via fast PIN scan on a branch terminal.
     * Searches active staff members assigned to the branch whose PIN matches.
     *
     * @throws AuthenticationException
     */
    public function authenticateStaffByPinOnly(string $organizationId, string $branchId, string $pin): StaffIdentity
    {
        $staffList = TenantContext::withoutTenancy(function () use ($organizationId) {
            return StaffIdentity::where('organization_id', $organizationId)
                ->where('status', 'active')
                ->get();
        });

        foreach ($staffList as $staff) {
            if ($staff->verifyPin($pin)) {
                if ($staff->isAssignedToBranch($branchId)) {
                    return $staff;
                }
                throw new AuthenticationException('Staff member is not authorized for this branch.');
            }
        }

        throw new AuthenticationException('No authorized staff member matches this PIN in this branch.');
    }
}
