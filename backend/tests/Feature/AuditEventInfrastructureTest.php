<?php

namespace Tests\Feature;

use App\Domain\Identity\Models\Branch;
use App\Domain\Identity\Models\Brand;
use App\Domain\Identity\Models\Organization;
use App\Domain\Identity\Models\RegisteredDevice;
use App\Domain\Identity\Services\DeviceRegistryService;
use App\Domain\Shared\Models\AuditEvent;
use App\Domain\Shared\Services\AuditService;
use App\Domain\Shared\Traits\RecordsAuditEvents;
use App\Domain\Shared\ValueObjects\AuditContext;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use LogicException;
use Tests\TestCase;

class AuditEventInfrastructureTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $org;
    protected Brand $brand;
    protected Branch $branch;
    protected AuditService $auditService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->auditService = app(AuditService::class);

        $this->org = Organization::create([
            'name' => 'Artisan Audit Org',
            'slug' => 'artisan-audit-org',
            'commercial_status' => 'active',
        ]);

        $this->brand = Brand::create([
            'organization_id' => $this->org->id,
            'name' => 'Artisan Coffee',
            'slug' => 'artisan-coffee',
        ]);

        $this->branch = Branch::create([
            'organization_id' => $this->org->id,
            'brand_id' => $this->brand->id,
            'code' => 'AUD-01',
            'name' => 'Audit Branch',
            'status' => 'active',
        ]);
    }

    public function test_audit_event_can_be_recorded_from_audit_context(): void
    {
        $context = AuditContext::create(
            organizationId: $this->org->id,
            actionKey: AuditEvent::ACTION_PRICE_OVERRIDE,
            targetType: 'OrderItem',
            targetId: (string) Str::uuid7(),
            actorId: 'staff-123',
            actorType: AuditContext::ACTOR_STAFF,
            branchId: $this->branch->id,
            deviceId: (string) Str::uuid7(),
            reason: 'Customer goodwill VIP discount',
            payload: [
                'original_price' => 15000,
                'override_price' => 10000,
                'supervisor_id' => 'mgr-456',
            ]
        );

        $event = $this->auditService->record($context);

        $this->assertInstanceOf(AuditEvent::class, $event);
        $this->assertDatabaseHas('audit_events', [
            'id' => $event->id,
            'organization_id' => $this->org->id,
            'branch_id' => $this->branch->id,
            'action_key' => AuditEvent::ACTION_PRICE_OVERRIDE,
            'actor_id' => 'staff-123',
            'actor_type' => AuditContext::ACTOR_STAFF,
            'reason' => 'Customer goodwill VIP discount',
        ]);

        $this->assertSame(15000, $event->payload['original_price']);
        $this->assertSame(10000, $event->payload['override_price']);
    }

    public function test_audit_event_is_strictly_immutable_and_rejects_updates(): void
    {
        $context = AuditContext::create(
            organizationId: $this->org->id,
            actionKey: AuditEvent::ACTION_DISCOUNT_APPLIED,
            targetType: 'Bill',
            targetId: (string) Str::uuid7(),
            actorId: 'staff-789',
            actorType: AuditContext::ACTOR_STAFF,
            branchId: $this->branch->id,
            reason: 'Happy Hour 10%'
        );

        $event = $this->auditService->record($context);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Audit events are strictly append-only and cannot be mutated.');

        $event->update(['reason' => 'Attempted tampering with audit reason']);
    }

    public function test_audit_event_is_strictly_immutable_and_rejects_deletions(): void
    {
        $context = AuditContext::create(
            organizationId: $this->org->id,
            actionKey: AuditEvent::ACTION_CASH_DRAWER_OPENED,
            targetType: 'CashDrawer',
            targetId: (string) Str::uuid7(),
            actorId: 'staff-999',
            actorType: AuditContext::ACTOR_STAFF,
            branchId: $this->branch->id,
            reason: 'No sale cash drawer pop'
        );

        $event = $this->auditService->record($context);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Audit events are strictly append-only and cannot be deleted.');

        $event->delete();
    }

    public function test_sensitive_data_is_automatically_redacted_in_audit_payload(): void
    {
        $targetId = (string) Str::uuid7();

        $event = $this->auditService->recordSensitiveAction(
            organizationId: $this->org->id,
            actionKey: AuditEvent::ACTION_AUTH_SECURITY,
            targetType: 'StaffIdentity',
            targetId: $targetId,
            reason: 'Staff PIN changed',
            before: [
                'pin' => '1234',
                'pin_hash' => 'hash_old_secret',
                'display_name' => 'Old Name',
            ],
            after: [
                'pin' => '9876',
                'pin_hash' => 'hash_new_secret',
                'display_name' => 'New Name',
                'device_token' => 'bnd_super_secret_token_12345',
            ],
            branchId: $this->branch->id,
            actorId: 'admin-001',
            actorType: AuditContext::ACTOR_USER
        );

        $payload = $event->payload;

        // Verify sensitive keys are redacted
        $this->assertSame('[REDACTED]', $payload['before']['pin']);
        $this->assertSame('[REDACTED]', $payload['before']['pin_hash']);
        $this->assertSame('[REDACTED]', $payload['after']['pin']);
        $this->assertSame('[REDACTED]', $payload['after']['pin_hash']);
        $this->assertSame('[REDACTED]', $payload['after']['device_token']);

        // Non-sensitive values remain intact
        $this->assertSame('Old Name', $payload['before']['display_name']);
        $this->assertSame('New Name', $payload['after']['display_name']);

        // Diff computed
        $this->assertContains('display_name', $payload['changed_keys']);
        $this->assertSame('Old Name', $payload['diff']['display_name']['from']);
        $this->assertSame('New Name', $payload['diff']['display_name']['to']);
    }

    public function test_device_lifecycle_actions_record_canonical_audit_events(): void
    {
        $deviceService = app(DeviceRegistryService::class);

        // 1. Enrollment records ACTION_DEVICE_REGISTERED
        $enrollResult = $deviceService->enroll([
            'organization_id' => $this->org->id,
            'branch_id' => $this->branch->id,
            'device_code' => 'AUDIT-DEV-01',
            'name' => 'Barista POS 1',
        ]);

        /** @var RegisteredDevice $device */
        $device = $enrollResult['device'];

        $this->assertDatabaseHas('audit_events', [
            'organization_id' => $this->org->id,
            'branch_id' => $this->branch->id,
            'action_key' => AuditEvent::ACTION_DEVICE_REGISTERED,
            'target_type' => 'RegisteredDevice',
            'target_id' => $device->id,
        ]);

        // 2. Credential rotation records ACTION_AUTH_SECURITY
        $deviceService->rotateCredentials($device);

        $this->assertDatabaseHas('audit_events', [
            'organization_id' => $this->org->id,
            'action_key' => AuditEvent::ACTION_AUTH_SECURITY,
            'target_type' => 'RegisteredDevice',
            'target_id' => $device->id,
            'reason' => 'Device credential rotation',
        ]);

        // 3. Revocation records ACTION_DEVICE_REVOKED
        $deviceService->revoke($device, 'Device decommissioning after hardware upgrade');

        $this->assertDatabaseHas('audit_events', [
            'organization_id' => $this->org->id,
            'action_key' => AuditEvent::ACTION_DEVICE_REVOKED,
            'target_type' => 'RegisteredDevice',
            'target_id' => $device->id,
            'reason' => 'Device decommissioning after hardware upgrade',
        ]);
    }

    public function test_audit_service_query_filtering(): void
    {
        $targetA = (string) Str::uuid7();
        $targetB = (string) Str::uuid7();

        $this->auditService->recordSensitiveAction(
            organizationId: $this->org->id,
            actionKey: AuditEvent::ACTION_PRICE_OVERRIDE,
            targetType: 'Product',
            targetId: $targetA,
            branchId: $this->branch->id,
            actorId: 'actor-1'
        );

        $this->auditService->recordSensitiveAction(
            organizationId: $this->org->id,
            actionKey: AuditEvent::ACTION_STOCK_ADJUSTMENT,
            targetType: 'InventoryItem',
            targetId: $targetB,
            branchId: $this->branch->id,
            actorId: 'actor-2'
        );

        // Query by action key
        $priceEvents = $this->auditService->query($this->org->id, [
            'action_key' => AuditEvent::ACTION_PRICE_OVERRIDE,
        ])->get();

        $this->assertCount(1, $priceEvents);
        $this->assertSame(AuditEvent::ACTION_PRICE_OVERRIDE, $priceEvents->first()->action_key);

        // Query by actor
        $actor2Events = $this->auditService->query($this->org->id, [
            'actor_id' => 'actor-2',
        ])->get();

        $this->assertCount(1, $actor2Events);
        $this->assertSame('actor-2', $actor2Events->first()->actor_id);
    }

    public function test_records_audit_events_trait(): void
    {
        $dummyService = new class {
            use RecordsAuditEvents;

            public function performVoid(string $orgId, string $orderId, string $reason): AuditEvent
            {
                return $this->recordAudit(
                    organizationId: $orgId,
                    actionKey: AuditEvent::ACTION_ITEM_VOIDED,
                    targetType: 'Order',
                    targetId: $orderId,
                    reason: $reason,
                    before: ['status' => 'pending'],
                    after: ['status' => 'voided']
                );
            }
        };

        $orderId = (string) Str::uuid7();
        $event = $dummyService->performVoid($this->org->id, $orderId, 'Customer changed order');

        $this->assertInstanceOf(AuditEvent::class, $event);
        $this->assertSame(AuditEvent::ACTION_ITEM_VOIDED, $event->action_key);
        $this->assertSame('Customer changed order', $event->reason);
        $this->assertSame('pending', $event->payload['before']['status']);
        $this->assertSame('voided', $event->payload['after']['status']);
    }
}
