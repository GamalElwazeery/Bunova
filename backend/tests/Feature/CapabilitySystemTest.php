<?php

namespace Tests\Feature;

use App\Domain\Capability\Contracts\CapabilityDefinition;
use App\Domain\Capability\Exceptions\CapabilityConflictException;
use App\Domain\Capability\Exceptions\CapabilityDependencyException;
use App\Domain\Capability\Models\CapabilityConfiguration;
use App\Domain\Capability\Services\CapabilityManager;
use App\Domain\Capability\Services\CapabilityRegistry;
use App\Domain\Identity\Models\Branch;
use App\Domain\Identity\Models\Brand;
use App\Domain\Identity\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class CapabilitySystemTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $org;
    protected Branch $branch1;
    protected Branch $branch2;
    protected CapabilityManager $capabilityManager;
    protected CapabilityRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = new CapabilityRegistry();
        $this->capabilityManager = new CapabilityManager($this->registry);

        $this->org = Organization::create([
            'name' => 'Signature Coffee & Gaming',
            'slug' => 'signature-coffee',
            'commercial_status' => 'active',
        ]);

        $brand = Brand::create([
            'organization_id' => $this->org->id,
            'name' => 'Signature Brand',
            'slug' => 'signature-brand',
        ]);

        $this->branch1 = Branch::create([
            'organization_id' => $this->org->id,
            'brand_id' => $brand->id,
            'name' => 'Main Hub Branch',
            'code' => 'HUB-01',
            'status' => 'active',
        ]);

        $this->branch2 = Branch::create([
            'organization_id' => $this->org->id,
            'brand_id' => $brand->id,
            'name' => 'Express Kiosk',
            'code' => 'EX-02',
            'status' => 'active',
        ]);
    }

    public function test_core_capabilities_are_always_enabled_and_cannot_be_disabled(): void
    {
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'pos'));
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'orders'));
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'billing'));
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'cash-shifts'));

        $this->expectException(InvalidArgumentException::class);
        $this->capabilityManager->disable($this->org, 'pos');
    }

    public function test_cannot_enable_capability_without_dependencies(): void
    {
        // 'gaming' depends on 'timed-resources', 'billing', and 'venue.sessions'
        // 'timed-resources' and 'venue.sessions' are not enabled yet
        $this->expectException(CapabilityDependencyException::class);
        $this->capabilityManager->enable($this->org, 'gaming');
    }

    public function test_can_enable_capability_atomically_with_dependencies(): void
    {
        // Enable 'gaming' with atomic = true
        $gamingConfig = $this->capabilityManager->enable(
            $this->org,
            'gaming',
            branchId: null,
            config: ['default_station_rate' => 50],
            atomic: true
        );

        $this->assertEquals(CapabilityConfiguration::STATUS_ENABLED, $gamingConfig->status);
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'gaming'));

        // All transitive dependencies must also be enabled
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'timed-resources'));
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'venue.sessions'));
    }

    public function test_capability_enters_needs_configuration_when_required_config_missing(): void
    {
        // 'wifi' requires 'router_host' and 'router_secret'
        $wifiConfig = $this->capabilityManager->enable($this->org, 'wifi', null, []);

        $this->assertEquals(CapabilityConfiguration::STATUS_NEEDS_CONFIGURATION, $wifiConfig->status);
        // While in needs_configuration, isEnabled must be false
        $this->assertFalse($this->capabilityManager->isEnabled($this->org, 'wifi'));
        $this->assertEquals('needs_configuration', $this->capabilityManager->getStatus($this->org, 'wifi'));

        // Now supply complete required configuration
        $updatedWifi = $this->capabilityManager->enable($this->org, 'wifi', null, [
            'router_host' => '192.168.1.1',
            'router_secret' => 'MikroTikSecretPass2026',
        ]);

        $this->assertEquals(CapabilityConfiguration::STATUS_ENABLED, $updatedWifi->status);
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'wifi'));
    }

    public function test_cannot_disable_capability_with_active_dependents(): void
    {
        // Enable gaming and its dependencies atomically
        $this->capabilityManager->enable($this->org, 'gaming', null, [], atomic: true);

        // Attempting to disable 'timed-resources' directly should fail because 'gaming' depends on it
        $this->expectException(CapabilityDependencyException::class);
        $this->capabilityManager->disable($this->org, 'timed-resources');
    }

    public function test_force_disable_cascade_disables_dependents(): void
    {
        $this->capabilityManager->enable($this->org, 'gaming', null, [], atomic: true);

        // Force disable 'timed-resources'
        $this->capabilityManager->disable(
            $this->org,
            'timed-resources',
            null,
            reason: 'Decommissioning timed services',
            force: true
        );

        $this->assertFalse($this->capabilityManager->isEnabled($this->org, 'timed-resources'));
        $this->assertFalse($this->capabilityManager->isEnabled($this->org, 'gaming'));
    }

    public function test_historical_data_is_preserved_on_disable(): void
    {
        $this->capabilityManager->enable($this->org, 'production.kitchen', null, ['printer_id' => 'PRN-01']);
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'production.kitchen'));

        // Disable with reason
        $disabled = $this->capabilityManager->disable(
            $this->org,
            'production.kitchen',
            null,
            'Switching to central commissary kitchen'
        );

        $this->assertEquals(CapabilityConfiguration::STATUS_DISABLED, $disabled->status);
        $this->assertNotNull($disabled->disabled_at);
        $this->assertEquals('Switching to central commissary kitchen', $disabled->disable_reason);
        $this->assertEquals('PRN-01', $disabled->config['printer_id']);

        // Record still exists in database
        $this->assertDatabaseHas('capability_configurations', [
            'organization_id' => $this->org->id,
            'capability_key' => 'production.kitchen',
            'status' => 'disabled',
            'disable_reason' => 'Switching to central commissary kitchen',
        ]);
    }

    public function test_branch_override_and_inheritance(): void
    {
        // Enable kitchen organization-wide
        $this->capabilityManager->enable($this->org, 'production.kitchen');

        // Branch 1 has no override: inherits from organization (enabled)
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'production.kitchen', $this->branch1->id));
        $this->assertEquals('enabled', $this->capabilityManager->getStatus($this->org, 'production.kitchen', $this->branch1->id));

        // Branch 2 is an Express Kiosk without kitchen: explicitly disable for Branch 2
        $this->capabilityManager->disable($this->org, 'production.kitchen', $this->branch2->id, 'No kitchen on kiosk branch');

        $this->assertFalse($this->capabilityManager->isEnabled($this->org, 'production.kitchen', $this->branch2->id));
        $this->assertEquals('disabled', $this->capabilityManager->getStatus($this->org, 'production.kitchen', $this->branch2->id));

        // Branch 1 remains unaffected (still enabled)
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'production.kitchen', $this->branch1->id));
    }

    public function test_effective_capabilities_projection(): void
    {
        $this->capabilityManager->enable($this->org, 'venue.floors');
        $this->capabilityManager->enable($this->org, 'venue.tables', $this->branch1->id);

        $projection = $this->capabilityManager->getEffectiveCapabilities($this->org, $this->branch1->id);

        $this->assertArrayHasKey('pos', $projection);
        $this->assertEquals('enabled', $projection['pos']['status']);
        $this->assertFalse($projection['pos']['is_override']);

        $this->assertArrayHasKey('venue.floors', $projection);
        $this->assertEquals('enabled', $projection['venue.floors']['status']);
        $this->assertFalse($projection['venue.floors']['is_override']);

        $this->assertArrayHasKey('venue.tables', $projection);
        $this->assertEquals('enabled', $projection['venue.tables']['status']);
        $this->assertTrue($projection['venue.tables']['is_override']);

        $this->assertArrayHasKey('gaming', $projection);
        $this->assertEquals('disabled', $projection['gaming']['status']);
    }

    public function test_conflict_prevention(): void
    {
        // Register a custom conflicting pair in registry
        $this->registry->register(new CapabilityDefinition(
            key: 'mode.simple_kiosk',
            family: 'mode',
            name: 'Simple Kiosk Mode',
            description: 'Ultra simple single-register mode',
            conflicts: ['venue.tables', 'gaming']
        ));

        $this->capabilityManager->enable($this->org, 'venue.floors');
        $this->capabilityManager->enable($this->org, 'venue.tables');

        // Attempting to enable mode.simple_kiosk while venue.tables is active must throw CapabilityConflictException
        $this->expectException(CapabilityConflictException::class);
        $this->capabilityManager->enable($this->org, 'mode.simple_kiosk');
    }
}
