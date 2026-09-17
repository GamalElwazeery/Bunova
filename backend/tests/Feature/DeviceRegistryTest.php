<?php

namespace Tests\Feature;

use App\Domain\Identity\Models\Branch;
use App\Domain\Identity\Models\Brand;
use App\Domain\Identity\Models\Organization;
use App\Domain\Identity\Models\RegisteredDevice;
use App\Domain\Identity\Services\DeviceRegistryService;
use App\Domain\Identity\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceRegistryTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $orgA;
    protected Organization $orgB;
    protected Brand $brandA;
    protected Brand $brandB;
    protected Branch $branchA1;
    protected Branch $branchA2;
    protected Branch $branchB1;
    protected DeviceRegistryService $deviceService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->deviceService = app(DeviceRegistryService::class);

        // Org A with 2 branches
        $this->orgA = Organization::create([
            'name' => 'Artisan Roasters',
            'slug' => 'artisan-roasters',
            'commercial_status' => 'active',
        ]);

        $this->brandA = Brand::create([
            'organization_id' => $this->orgA->id,
            'name' => 'Artisan Brand',
            'slug' => 'artisan-brand',
        ]);

        $this->branchA1 = Branch::create([
            'organization_id' => $this->orgA->id,
            'brand_id' => $this->brandA->id,
            'name' => 'Downtown Branch',
            'code' => 'DT-01',
            'status' => 'active',
        ]);

        $this->branchA2 = Branch::create([
            'organization_id' => $this->orgA->id,
            'brand_id' => $this->brandA->id,
            'name' => 'Mall Branch',
            'code' => 'ML-02',
            'status' => 'active',
        ]);

        // Org B with 1 branch
        $this->orgB = Organization::create([
            'name' => 'Competitor Coffee',
            'slug' => 'competitor-coffee',
            'commercial_status' => 'active',
        ]);

        $this->brandB = Brand::create([
            'organization_id' => $this->orgB->id,
            'name' => 'Competitor Brand',
            'slug' => 'competitor-brand',
        ]);

        $this->branchB1 = Branch::create([
            'organization_id' => $this->orgB->id,
            'brand_id' => $this->brandB->id,
            'name' => 'Airport Branch',
            'code' => 'AP-01',
            'status' => 'active',
        ]);
    }

    protected function tearDown(): void
    {
        TenantContext::clear();
        parent::tearDown();
    }

    public function test_can_enroll_device_and_receive_credentials(): void
    {
        $payload = [
            'organization_id' => $this->orgA->id,
            'branch_id' => $this->branchA1->id,
            'device_code' => 'POS-01',
            'name' => 'Front Counter Register 1',
            'device_type' => RegisteredDevice::TYPE_POS_REGISTER,
            'capabilities' => [
                RegisteredDevice::CAPABILITY_RECEIPT_PRINTING,
                RegisteredDevice::CAPABILITY_CASH_DRAWER,
                RegisteredDevice::CAPABILITY_OFFLINE_ORDERS,
            ],
            'hardware_metadata' => [
                'platform' => 'linux',
                'os_version' => 'Ubuntu 24.04',
                'app_version' => '1.0.0',
            ],
        ];

        $response = $this->postJson('/api/v1/devices/enroll', $payload);

        $response->assertCreated();
        $response->assertJsonStructure([
            'message',
            'device' => [
                'id',
                'organization_id',
                'branch_id',
                'device_code',
                'name',
                'device_type',
                'status',
                'capabilities',
            ],
            'device_token',
        ]);

        $token = $response->json('device_token');
        $this->assertStringStartsWith('bnd_', $token);

        // Verify database persistence
        $device = RegisteredDevice::find($response->json('device.id'));
        $this->assertNotNull($device);
        $this->assertEquals('POS-01', $device->device_code);
        $this->assertEquals(hash('sha256', $token), $device->token_hash);
        $this->assertTrue($device->isActive());
        $this->assertTrue($device->hasCapability(RegisteredDevice::CAPABILITY_RECEIPT_PRINTING));
        $this->assertTrue($device->supportsOffline());
    }

    public function test_authenticated_device_can_access_me_and_heartbeat(): void
    {
        $enrollment = $this->deviceService->enroll([
            'organization_id' => $this->orgA->id,
            'branch_id' => $this->branchA1->id,
            'device_code' => 'POS-02',
            'name' => 'Drive-Thru Terminal',
            'device_type' => RegisteredDevice::TYPE_POS_REGISTER,
        ]);

        $token = $enrollment['token'];

        // Access /me via X-Device-Token header
        $response = $this->withHeaders([
            'X-Device-Token' => $token,
        ])->getJson('/api/v1/devices/me');

        $response->assertOk();
        $response->assertJsonPath('device.device_code', 'POS-02');
        $this->assertEquals($this->orgA->id, TenantContext::getOrganizationId());
        $this->assertEquals($this->branchA1->id, TenantContext::getBranchId());

        // Access /me via Bearer token format
        $responseBearer = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/devices/me');

        $responseBearer->assertOk();

        // Record heartbeat with updated telemetry
        $heartbeatResponse = $this->withHeaders([
            'X-Device-Token' => $token,
        ])->postJson('/api/v1/devices/heartbeat', [
            'hardware_metadata' => [
                'battery_level' => 98,
                'network' => 'wifi_5ghz',
            ],
        ]);

        $heartbeatResponse->assertOk();
        $heartbeatResponse->assertJson(['status' => 'ok']);

        $updatedDevice = $enrollment['device']->fresh();
        $this->assertNotNull($updatedDevice->last_seen_at);
        $this->assertEquals(98, $updatedDevice->hardware_metadata['battery_level']);
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        // No token provided
        $response = $this->getJson('/api/v1/devices/me');
        $response->assertUnauthorized();

        // Invalid token provided
        $responseBad = $this->withHeaders([
            'X-Device-Token' => 'bnd_invalid_token_12345678901234567890',
        ])->getJson('/api/v1/devices/me');
        $responseBad->assertUnauthorized();
    }

    public function test_device_revocation_workflow(): void
    {
        $enrollment = $this->deviceService->enroll([
            'organization_id' => $this->orgA->id,
            'branch_id' => $this->branchA1->id,
            'device_code' => 'POS-03',
            'name' => 'Stolen Tablet',
            'device_type' => RegisteredDevice::TYPE_HANDHELD_WAITER,
        ]);

        $device = $enrollment['device'];
        $token = $enrollment['token'];

        // Confirm device is initially active
        $response = $this->withHeaders(['X-Device-Token' => $token])->getJson('/api/v1/devices/me');
        $response->assertOk();

        // Revoke the device
        $revokeResponse = $this->postJson("/api/v1/devices/{$device->id}/revoke", [
            'reason' => 'Device reported lost or stolen by shift supervisor.',
        ]);

        $revokeResponse->assertOk();
        $revokeResponse->assertJson(['status' => 'revoked']);

        $device->refresh();
        $this->assertTrue($device->isRevoked());
        $this->assertNull($device->token_hash);
        $this->assertNotNull($device->revoked_at);
        $this->assertEquals('Device reported lost or stolen by shift supervisor.', $device->revocation_reason);

        // Previous token must now be rejected immediately
        $deniedResponse = $this->withHeaders(['X-Device-Token' => $token])->getJson('/api/v1/devices/me');
        $deniedResponse->assertUnauthorized();
    }

    public function test_suspended_device_is_rejected_until_reactivated(): void
    {
        $enrollment = $this->deviceService->enroll([
            'organization_id' => $this->orgA->id,
            'branch_id' => $this->branchA1->id,
            'device_code' => 'POS-04',
            'name' => 'Maintenance Terminal',
        ]);

        $device = $enrollment['device'];
        $token = $enrollment['token'];

        // Suspend device
        $this->deviceService->suspend($device, 'Scheduled hardware maintenance');
        $this->assertTrue($device->fresh()->isSuspended());

        // Authenticated request is rejected while suspended
        $response = $this->withHeaders(['X-Device-Token' => $token])->getJson('/api/v1/devices/me');
        $response->assertUnauthorized();

        // Reactivate device
        $this->deviceService->reactivate($device);
        $this->assertTrue($device->fresh()->isActive());

        // Request now succeeds
        $successResponse = $this->withHeaders(['X-Device-Token' => $token])->getJson('/api/v1/devices/me');
        $successResponse->assertOk();
    }

    public function test_cannot_enroll_duplicate_device_code_in_same_branch(): void
    {
        $this->deviceService->enroll([
            'organization_id' => $this->orgA->id,
            'branch_id' => $this->branchA1->id,
            'device_code' => 'KDS-01',
            'name' => 'Kitchen Station 1',
            'device_type' => RegisteredDevice::TYPE_KDS_KITCHEN,
        ]);

        // Attempt duplicate enrollment in same branch
        $response = $this->postJson('/api/v1/devices/enroll', [
            'organization_id' => $this->orgA->id,
            'branch_id' => $this->branchA1->id,
            'device_code' => 'KDS-01',
            'name' => 'Another Kitchen Station',
            'device_type' => RegisteredDevice::TYPE_KDS_KITCHEN,
        ]);

        $response->assertUnprocessable();

        // But same code in a different branch is allowed
        $responseBranch2 = $this->postJson('/api/v1/devices/enroll', [
            'organization_id' => $this->orgA->id,
            'branch_id' => $this->branchA2->id,
            'device_code' => 'KDS-01',
            'name' => 'Mall Kitchen Station',
            'device_type' => RegisteredDevice::TYPE_KDS_KITCHEN,
        ]);

        $responseBranch2->assertCreated();
    }

    public function test_cross_tenant_device_isolation(): void
    {
        // Attempting to enroll a device claiming Org A with Branch B1 (which belongs to Org B)
        $response = $this->postJson('/api/v1/devices/enroll', [
            'organization_id' => $this->orgA->id,
            'branch_id' => $this->branchB1->id, // Mismatched branch!
            'device_code' => 'POS-99',
            'name' => 'Cross Tenant Attempt',
        ]);

        $response->assertUnprocessable();
    }

    public function test_credential_rotation_invalidates_previous_token(): void
    {
        $enrollment = $this->deviceService->enroll([
            'organization_id' => $this->orgA->id,
            'branch_id' => $this->branchA1->id,
            'device_code' => 'POS-05',
            'name' => 'Secure Terminal',
        ]);

        $device = $enrollment['device'];
        $oldToken = $enrollment['token'];

        // Rotate credentials
        $rotateResponse = $this->postJson("/api/v1/devices/{$device->id}/rotate-credentials");
        $rotateResponse->assertOk();

        $newToken = $rotateResponse->json('device_token');
        $this->assertNotEquals($oldToken, $newToken);

        // Old token is rejected
        $oldResponse = $this->withHeaders(['X-Device-Token' => $oldToken])->getJson('/api/v1/devices/me');
        $oldResponse->assertUnauthorized();

        // New token is accepted
        $newResponse = $this->withHeaders(['X-Device-Token' => $newToken])->getJson('/api/v1/devices/me');
        $newResponse->assertOk();
    }
}
