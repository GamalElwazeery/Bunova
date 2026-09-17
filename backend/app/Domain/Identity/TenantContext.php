<?php

namespace App\Domain\Identity;

use App\Domain\Identity\Models\Branch;
use App\Domain\Identity\Models\Organization;

class TenantContext
{
    protected static ?string $organizationId = null;
    protected static ?Organization $organization = null;
    protected static ?string $branchId = null;
    protected static ?Branch $branch = null;
    protected static bool $bypassTenancy = false;

    public static function setOrganization(Organization|string $organization): void
    {
        if ($organization instanceof Organization) {
            self::$organization = $organization;
            self::$organizationId = $organization->id;
        } else {
            self::$organizationId = $organization;
            self::$organization = null;
        }
    }

    public static function getOrganizationId(): ?string
    {
        return self::$organizationId;
    }

    public static function getOrganization(): ?Organization
    {
        if (self::$organization === null && self::$organizationId !== null) {
            self::$organization = Organization::withoutGlobalScopes()->find(self::$organizationId);
        }
        return self::$organization;
    }

    public static function hasOrganization(): bool
    {
        return self::$organizationId !== null && !self::$bypassTenancy;
    }

    public static function setBranch(Branch|string|null $branch): void
    {
        if ($branch instanceof Branch) {
            self::$branch = $branch;
            self::$branchId = $branch->id;
            // Also ensure organization aligns
            if ($branch->organization_id && self::$organizationId !== $branch->organization_id) {
                self::setOrganization($branch->organization_id);
            }
        } elseif (is_string($branch)) {
            self::$branchId = $branch;
            self::$branch = null;
        } else {
            self::$branchId = null;
            self::$branch = null;
        }
    }

    public static function getBranchId(): ?string
    {
        return self::$branchId;
    }

    public static function getBranch(): ?Branch
    {
        if (self::$branch === null && self::$branchId !== null) {
            self::$branch = Branch::withoutGlobalScopes()->find(self::$branchId);
        }
        return self::$branch;
    }

    public static function hasBranch(): bool
    {
        return self::$branchId !== null && !self::$bypassTenancy;
    }

    public static function clear(): void
    {
        self::$organizationId = null;
        self::$organization = null;
        self::$branchId = null;
        self::$branch = null;
        self::$bypassTenancy = false;
    }

    /**
     * Execute callback bypassing global tenant scoping.
     *
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    public static function withoutTenancy(callable $callback): mixed
    {
        $previous = self::$bypassTenancy;
        self::$bypassTenancy = true;
        try {
            return $callback();
        } finally {
            self::$bypassTenancy = $previous;
        }
    }

    public static function isTenancyBypassed(): bool
    {
        return self::$bypassTenancy;
    }
}
