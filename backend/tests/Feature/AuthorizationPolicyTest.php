<?php

namespace Tests\Feature;

use App\Domain\Billing\Policies\BillPolicy;
use App\Domain\Identity\Models\Branch;
use App\Domain\Identity\Models\Organization;
use App\Domain\Identity\Models\Permission;
use App\Domain\Identity\Models\Role;
use App\Domain\Identity\Models\StaffIdentity;
use App\Domain\Identity\Services\AuthorizationService;
use App\Domain\Identity\Support\PermissionCatalog;
use App\Domain\Orders\Policies\OrderPolicy;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $orgA;
    protected Organization $orgB;
    protected Branch $branchA1;
    protected Branch $branchA2;
    protected Branch $branchB1;
    protected array $rolesA;
    protected AuthorizationService $authService;
    protected BillPolicy $billPolicy;
    protected OrderPolicy $orderPolicy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authService = app(AuthorizationService::class);
        $this->billPolicy = app(BillPolicy::class);
        $this->orderPolicy = app(OrderPolicy::class);

        // Create Org A with Brand and 2 branches
        $this->orgA = Organization::create([
            'name' => 'Artisan Roasters',
            'slug' => 'artisan-roasters',
            'commercial_status' => 'active',
        ]);

        $brandA = \App\Domain\Identity\Models\Brand::create([
            'organization_id' => $this->orgA->id,
            'name' => 'Artisan Main Brand',
            'slug' => 'artisan-main',
        ]);

        $this->branchA1 = Branch::create([
            'organization_id' => $this->orgA->id,
            'brand_id' => $brandA->id,
            'name' => 'Downtown Branch',
            'code' => 'DT-01',
            'status' => 'active',
        ]);

        $this->branchA2 = Branch::create([
            'organization_id' => $this->orgA->id,
            'brand_id' => $brandA->id,
            'name' => 'Mall Branch',
            'code' => 'ML-02',
            'status' => 'active',
        ]);

        // Create Org B with Brand and 1 branch
        $this->orgB = Organization::create([
            'name' => 'Competitor Coffee',
            'slug' => 'competitor-coffee',
            'commercial_status' => 'active',
        ]);

        $brandB = \App\Domain\Identity\Models\Brand::create([
            'organization_id' => $this->orgB->id,
            'name' => 'Competitor Brand',
            'slug' => 'competitor-brand',
        ]);

        $this->branchB1 = Branch::create([
            'organization_id' => $this->orgB->id,
            'brand_id' => $brandB->id,
            'name' => 'Airport Branch',
            'code' => 'AP-01',
            'status' => 'active',
        ]);

        // Seed permissions and standard roles for Org A
        $this->rolesA = PermissionCatalog::seedOrganizationRoles($this->orgA);
    }

    protected function createStaff(array $attributes): StaffIdentity
    {
        return StaffIdentity::create(array_merge([
            'pin_hash' => \Illuminate\Support\Facades\Hash::make('1234'),
            'status' => 'active',
        ], $attributes));
    }

    public function test_cashier_can_create_orders_and_finalize_bills_on_assigned_branch(): void
    {
        $cashierStaff = $this->createStaff([
            'organization_id' => $this->orgA->id,
            'primary_branch_id' => $this->branchA1->id,
            'staff_code' => 'STF-101',
            'display_name' => 'Alice Cashier',
            'status' => 'active',
        ]);

        // Assign Cashier role scoped to Branch A1
        $cashierStaff->assignRole($this->rolesA['cashier'], $this->branchA1->id);

        // Allowed actions on Branch A1
        $this->assertTrue($this->authService->canStaff($cashierStaff, PermissionCatalog::ORDERS_CREATE, $this->branchA1->id));
        $this->assertTrue($this->authService->canStaff($cashierStaff, PermissionCatalog::BILLS_FINALIZE, $this->branchA1->id));

        // Policy checks
        $this->assertTrue($this->orderPolicy->create($cashierStaff, $this->branchA1->id));
        $this->assertTrue($this->billPolicy->finalize($cashierStaff, $this->branchA1->id));
    }

    public function test_cashier_is_denied_actions_on_unassigned_branch(): void
    {
        $cashierStaff = $this->createStaff([
            'organization_id' => $this->orgA->id,
            'primary_branch_id' => $this->branchA1->id,
            'staff_code' => 'STF-102',
            'display_name' => 'Bob Cashier',
            'status' => 'active',
        ]);

        // Assign Cashier role ONLY to Branch A1
        $cashierStaff->assignRole($this->rolesA['cashier'], $this->branchA1->id);

        // Denied on Branch A2 (negative branch scoping)
        $this->assertFalse($this->authService->canStaff($cashierStaff, PermissionCatalog::ORDERS_CREATE, $this->branchA2->id));
        $this->assertFalse($this->authService->canStaff($cashierStaff, PermissionCatalog::BILLS_FINALIZE, $this->branchA2->id));

        // Policy checks deny on unassigned branch
        $this->assertFalse($this->orderPolicy->create($cashierStaff, $this->branchA2->id));
        $this->assertFalse($this->billPolicy->finalize($cashierStaff, $this->branchA2->id));
    }

    public function test_cashier_is_denied_supervisor_actions(): void
    {
        $cashierStaff = $this->createStaff([
            'organization_id' => $this->orgA->id,
            'primary_branch_id' => $this->branchA1->id,
            'staff_code' => 'STF-103',
            'display_name' => 'Charlie Cashier',
            'status' => 'active',
        ]);

        $cashierStaff->assignRole($this->rolesA['cashier'], $this->branchA1->id);

        // Supervisor actions must be denied
        $this->assertFalse($this->authService->canStaff($cashierStaff, PermissionCatalog::BILLS_VOID, $this->branchA1->id));
        $this->assertFalse($this->authService->canStaff($cashierStaff, PermissionCatalog::ORDERS_DISCOUNT, $this->branchA1->id));
        $this->assertFalse($this->authService->canStaff($cashierStaff, PermissionCatalog::ORDERS_VOID_ITEM, $this->branchA1->id));
        $this->assertFalse($this->authService->canStaff($cashierStaff, PermissionCatalog::PAYMENTS_REFUND, $this->branchA1->id));

        // Policy checks
        $this->assertFalse($this->billPolicy->void($cashierStaff, $this->branchA1->id));
        $this->assertFalse($this->orderPolicy->applyDiscount($cashierStaff, $this->branchA1->id));
        $this->assertFalse($this->orderPolicy->voidItem($cashierStaff, $this->branchA1->id));
    }

    public function test_branch_manager_can_perform_supervisor_actions_on_their_branch(): void
    {
        $managerStaff = $this->createStaff([
            'organization_id' => $this->orgA->id,
            'primary_branch_id' => $this->branchA1->id,
            'staff_code' => 'MGR-201',
            'display_name' => 'David Manager',
            'status' => 'active',
        ]);

        $managerStaff->assignRole($this->rolesA['branch_manager'], $this->branchA1->id);

        // Branch Manager has supervisor permissions on Branch A1
        $this->assertTrue($this->authService->canStaff($managerStaff, PermissionCatalog::BILLS_VOID, $this->branchA1->id));
        $this->assertTrue($this->authService->canStaff($managerStaff, PermissionCatalog::ORDERS_DISCOUNT, $this->branchA1->id));
        $this->assertTrue($this->authService->canStaff($managerStaff, PermissionCatalog::PAYMENTS_REFUND, $this->branchA1->id));

        // Policy checks
        $this->assertTrue($this->billPolicy->void($managerStaff, $this->branchA1->id));
        $this->assertTrue($this->orderPolicy->applyDiscount($managerStaff, $this->branchA1->id));

        // But denied on Branch A2
        $this->assertFalse($this->authService->canStaff($managerStaff, PermissionCatalog::BILLS_VOID, $this->branchA2->id));
    }

    public function test_owner_with_organization_wide_scope_can_access_all_org_branches(): void
    {
        $ownerStaff = $this->createStaff([
            'organization_id' => $this->orgA->id,
            'primary_branch_id' => $this->branchA1->id,
            'staff_code' => 'OWN-001',
            'display_name' => 'Owner Oliver',
            'status' => 'active',
        ]);

        // Assign Owner role with null branch_id (organization-wide)
        $ownerStaff->assignRole($this->rolesA['owner'], null);

        // Permitted across all branches of Org A
        $this->assertTrue($this->authService->canStaff($ownerStaff, PermissionCatalog::BILLS_VOID, $this->branchA1->id));
        $this->assertTrue($this->authService->canStaff($ownerStaff, PermissionCatalog::BILLS_VOID, $this->branchA2->id));
        $this->assertTrue($this->authService->canStaff($ownerStaff, PermissionCatalog::ORDERS_CREATE, $this->branchA1->id));
        $this->assertTrue($this->authService->canStaff($ownerStaff, PermissionCatalog::ORDERS_CREATE, $this->branchA2->id));
    }

    public function test_inactive_staff_is_denied_all_actions(): void
    {
        $inactiveStaff = $this->createStaff([
            'organization_id' => $this->orgA->id,
            'primary_branch_id' => $this->branchA1->id,
            'staff_code' => 'STF-999',
            'display_name' => 'Inactive Irene',
            'status' => 'inactive', // Deactivated
        ]);

        $inactiveStaff->assignRole($this->rolesA['owner'], null);

        // Denied despite having owner role because status is inactive
        $this->assertFalse($this->authService->canStaff($inactiveStaff, PermissionCatalog::ORDERS_CREATE, $this->branchA1->id));
        $this->assertFalse($this->authService->canStaff($inactiveStaff, PermissionCatalog::BILLS_FINALIZE, $this->branchA1->id));
        $this->assertFalse($this->authService->canStaff($inactiveStaff, PermissionCatalog::BILLS_VOID, $this->branchA1->id));

        $this->expectException(AuthorizationException::class);
        $this->authService->authorizeStaff($inactiveStaff, PermissionCatalog::ORDERS_CREATE, $this->branchA1->id);
    }

    public function test_staff_cannot_perform_actions_on_different_organization_branch(): void
    {
        $ownerStaff = $this->createStaff([
            'organization_id' => $this->orgA->id,
            'primary_branch_id' => $this->branchA1->id,
            'staff_code' => 'OWN-002',
            'display_name' => 'Owner Oliver',
            'status' => 'active',
        ]);

        $ownerStaff->assignRole($this->rolesA['owner'], null);

        // Org B's branch: strictly denied even with org-wide role on Org A
        $this->assertFalse($this->authService->canStaff($ownerStaff, PermissionCatalog::ORDERS_CREATE, $this->branchB1->id));
    }

    public function test_platform_permissions_are_strictly_denied_to_operational_staff_and_tenant_roles(): void
    {
        $ownerStaff = $this->createStaff([
            'organization_id' => $this->orgA->id,
            'primary_branch_id' => $this->branchA1->id,
            'staff_code' => 'OWN-003',
            'display_name' => 'Owner Oliver',
            'status' => 'active',
        ]);

        $ownerStaff->assignRole($this->rolesA['owner'], null);

        // Attempting platform permission
        $this->assertFalse($this->authService->canStaff($ownerStaff, PermissionCatalog::PLATFORM_ADMIN));
        $this->assertFalse($this->authService->canStaff($ownerStaff, PermissionCatalog::PLATFORM_TENANTS));
        $this->assertFalse($this->authService->canStaff($ownerStaff, PermissionCatalog::PLATFORM_BILLING));

        // Tenant user cannot perform platform actions
        $tenantUser = User::create([
            'organization_id' => $this->orgA->id,
            'name' => 'Tenant Owner User',
            'email' => 'owner@artisan.com',
            'password' => 'secret123',
            'user_type' => User::TYPE_TENANT_USER,
            'status' => 'active',
        ]);

        $this->assertFalse($this->authService->canUser($tenantUser, PermissionCatalog::PLATFORM_ADMIN));
        $this->assertFalse($this->authService->canUser($tenantUser, PermissionCatalog::PLATFORM_TENANTS));

        // Platform operator CAN perform platform actions
        $platformUser = User::create([
            'name' => 'Platform Operator',
            'email' => 'operator@bunova.cloud',
            'password' => 'platformSecret123',
            'user_type' => User::TYPE_PLATFORM_OPERATOR,
            'status' => 'active',
        ]);

        $this->assertTrue($this->authService->canUser($platformUser, PermissionCatalog::PLATFORM_ADMIN));
        $this->assertTrue($this->authService->canUser($platformUser, PermissionCatalog::PLATFORM_TENANTS));
    }
}
