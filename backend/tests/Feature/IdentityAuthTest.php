<?php

namespace Tests\Feature;

use App\Domain\Identity\Models\Branch;
use App\Domain\Identity\Models\Brand;
use App\Domain\Identity\Models\Organization;
use App\Domain\Identity\Models\StaffIdentity;
use App\Domain\Identity\Services\AuthenticationService;
use App\Domain\Identity\TenantContext;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IdentityAuthTest extends TestCase
{
    use RefreshDatabase;

    protected AuthenticationService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthenticationService();
    }

    protected function tearDown(): void
    {
        TenantContext::clear();
        parent::tearDown();
    }

    public function test_user_authentication_success_and_failure(): void
    {
        $org = Organization::create(['slug' => 'auth-org', 'name' => 'Auth Org']);

        $user = User::create([
            'organization_id' => $org->id,
            'name' => 'Tarek Manager',
            'email' => 'tarek@example.com',
            'password' => Hash::make('CorrectPassword123!'),
            'user_type' => User::TYPE_TENANT_USER,
            'status' => 'active',
        ]);

        // Success case
        $authenticated = $this->authService->authenticateUser('tarek@example.com', 'CorrectPassword123!');
        $this->assertEquals($user->id, $authenticated->id);
        $this->assertTrue($authenticated->isTenantUser());
        $this->assertFalse($authenticated->isPlatformOperator());

        // Incorrect password fails
        $this->expectException(AuthenticationException::class);
        $this->authService->authenticateUser('tarek@example.com', 'WrongPassword!');
    }

    public function test_suspended_user_authentication_fails(): void
    {
        $org = Organization::create(['slug' => 'susp-org', 'name' => 'Suspended User Org']);

        User::create([
            'organization_id' => $org->id,
            'name' => 'Suspended User',
            'email' => 'suspended@example.com',
            'password' => Hash::make('Password123!'),
            'user_type' => User::TYPE_TENANT_USER,
            'status' => 'suspended',
        ]);

        $this->expectException(AuthenticationException::class);
        $this->authService->authenticateUser('suspended@example.com', 'Password123!');
    }

    public function test_platform_operator_identity_separation(): void
    {
        $operator = User::create([
            'organization_id' => null,
            'name' => 'Platform Admin Operator',
            'email' => 'ops@bunova.app',
            'password' => Hash::make('PlatformSecurePassword123!'),
            'user_type' => User::TYPE_PLATFORM_OPERATOR,
            'status' => 'active',
        ]);

        $this->assertTrue($operator->isPlatformOperator());
        $this->assertFalse($operator->isTenantUser());
        $this->assertNull($operator->organization_id);

        $authenticated = $this->authService->authenticateUser('ops@bunova.app', 'PlatformSecurePassword123!');
        $this->assertEquals($operator->id, $authenticated->id);
        $this->assertTrue($authenticated->isPlatformOperator());
    }

    public function test_staff_pin_authentication_and_branch_scoping(): void
    {
        $org = Organization::create(['slug' => 'cafe-staff-org', 'name' => 'Staff Org']);
        $brand = Brand::create(['organization_id' => $org->id, 'slug' => 'main-brand', 'name' => 'Brand']);

        $branchA = Branch::create(['organization_id' => $org->id, 'brand_id' => $brand->id, 'code' => 'BR-A', 'name' => 'Branch A']);
        $branchB = Branch::create(['organization_id' => $org->id, 'brand_id' => $brand->id, 'code' => 'BR-B', 'name' => 'Branch B']);

        $staff = new StaffIdentity([
            'organization_id' => $org->id,
            'primary_branch_id' => $branchA->id,
            'staff_code' => 'CASH-01',
            'display_name' => 'Omar Cashier',
            'role_title' => 'cashier',
            'status' => 'active',
        ]);
        $staff->setPin('1234');
        $staff->save();

        // 1. Success on primary branch A
        $authStaff = $this->authService->authenticateStaff($org->id, $branchA->id, 'CASH-01', '1234');
        $this->assertEquals($staff->id, $authStaff->id);
        $this->assertTrue($authStaff->verifyPin('1234'));
        $this->assertFalse($authStaff->verifyPin('0000'));

        // 2. PIN-only quick authenticate on branch A
        $authPinOnly = $this->authService->authenticateStaffByPinOnly($org->id, $branchA->id, '1234');
        $this->assertEquals($staff->id, $authPinOnly->id);

        // 3. Fails on branch B where staff is NOT assigned
        $this->expectException(AuthenticationException::class);
        $this->authService->authenticateStaff($org->id, $branchB->id, 'CASH-01', '1234');
    }

    public function test_cross_tenant_staff_pin_isolation(): void
    {
        $org1 = Organization::create(['slug' => 'org-one', 'name' => 'Org 1']);
        $brand1 = Brand::create(['organization_id' => $org1->id, 'slug' => 'b1', 'name' => 'B1']);
        $branch1 = Branch::create(['organization_id' => $org1->id, 'brand_id' => $brand1->id, 'code' => 'B1-01', 'name' => 'Branch 1']);

        $org2 = Organization::create(['slug' => 'org-two', 'name' => 'Org 2']);
        $brand2 = Brand::create(['organization_id' => $org2->id, 'slug' => 'b2', 'name' => 'B2']);
        $branch2 = Branch::create(['organization_id' => $org2->id, 'brand_id' => $brand2->id, 'code' => 'B2-01', 'name' => 'Branch 2']);

        // Staff in Org 1 has PIN 5555
        $staff1 = new StaffIdentity([
            'organization_id' => $org1->id,
            'primary_branch_id' => $branch1->id,
            'staff_code' => 'ST-01',
            'display_name' => 'Org 1 Staff',
        ]);
        $staff1->setPin('5555');
        $staff1->save();

        // Testing staff from Org 1 trying to authenticate on Org 2 terminal with same PIN
        $this->expectException(AuthenticationException::class);
        $this->authService->authenticateStaffByPinOnly($org2->id, $branch2->id, '5555');
    }

    public function test_staff_api_endpoint_success_and_failure(): void
    {
        $org = Organization::create(['slug' => 'api-org', 'name' => 'API Org']);
        $brand = Brand::create(['organization_id' => $org->id, 'slug' => 'api-brand', 'name' => 'API Brand']);
        $branch = Branch::create(['organization_id' => $org->id, 'brand_id' => $brand->id, 'code' => 'BR-01', 'name' => 'API Branch']);

        $staff = new StaffIdentity([
            'organization_id' => $org->id,
            'primary_branch_id' => $branch->id,
            'staff_code' => 'OP-99',
            'display_name' => 'Nour Barista',
            'role_title' => 'barista',
        ]);
        $staff->setPin('9876');
        $staff->save();

        // Valid API call
        $response = $this->postJson('/api/v1/pos/staff/authenticate', [
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'staff_code' => 'OP-99',
            'pin' => '9876',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'authenticated',
                'staff' => [
                    'id' => $staff->id,
                    'staff_code' => 'OP-99',
                    'display_name' => 'Nour Barista',
                    'role_title' => 'barista',
                    'organization_id' => $org->id,
                    'branch_id' => $branch->id,
                ],
            ]);

        // Invalid PIN call
        $badResponse = $this->postJson('/api/v1/pos/staff/authenticate', [
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'staff_code' => 'OP-99',
            'pin' => '0000',
        ]);

        $badResponse->assertStatus(401)
            ->assertJson([
                'status' => 'unauthenticated',
            ]);
    }
}
