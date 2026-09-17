<?php

namespace Tests\Feature;

use App\Domain\Capability\Models\CapabilityConfiguration;
use App\Domain\Capability\Services\CapabilityManager;
use App\Domain\Capability\Services\CapabilityRegistry;
use App\Domain\Capability\Services\PresetRegistry;
use App\Domain\Capability\Services\PresetService;
use App\Domain\Identity\Models\Branch;
use App\Domain\Identity\Models\Brand;
use App\Domain\Identity\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingPresetTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $org;
    protected Branch $branch1;
    protected Branch $branch2;
    protected PresetService $presetService;
    protected CapabilityManager $capabilityManager;
    protected PresetRegistry $presetRegistry;

    protected function setUp(): void
    {
        parent::setUp();

        $capabilityRegistry = new CapabilityRegistry();
        $this->capabilityManager = new CapabilityManager($capabilityRegistry);
        $this->presetRegistry = new PresetRegistry();
        $this->presetService = new PresetService($this->presetRegistry, $this->capabilityManager);

        $this->org = Organization::create([
            'name' => 'Signature Venues Group',
            'slug' => 'signature-venues',
            'commercial_status' => 'active',
            'settings' => ['initial_setup' => true],
        ]);

        $brand = Brand::create([
            'organization_id' => $this->org->id,
            'name' => 'Signature Coffee & Gaming',
            'slug' => 'signature-brand',
        ]);

        $this->branch1 = Branch::create([
            'organization_id' => $this->org->id,
            'brand_id' => $brand->id,
            'name' => 'Flagship Lounge Branch',
            'code' => 'FL-01',
            'status' => 'active',
        ]);

        $this->branch2 = Branch::create([
            'organization_id' => $this->org->id,
            'brand_id' => $brand->id,
            'name' => 'Express Kiosk Branch',
            'code' => 'EX-02',
            'status' => 'active',
        ]);
    }

    public function test_all_canonical_presets_are_registered_and_have_valid_dependencies(): void
    {
        $expectedPresets = [
            'coffee_cart',
            'takeaway_kiosk',
            'coffee_shop',
            'traditional_cafe',
            'cafe_and_restaurant',
            'gaming_cafe',
            'coworking_cafe',
            'multi_branch_chain',
        ];

        foreach ($expectedPresets as $presetId) {
            $this->assertTrue(
                $this->presetRegistry->has($presetId),
                "Preset '{$presetId}' must be registered in PresetRegistry."
            );
        }

        // Validate all dependency edges across every preset
        $errors = $this->presetService->validateAllPresets();
        $this->assertEmpty($errors, 'All presets must have valid, resolvable dependency graphs: ' . json_encode($errors));
    }

    public function test_can_apply_preset_to_organization_and_enable_capabilities(): void
    {
        $result = $this->presetService->apply($this->org, 'coffee_shop');

        $this->assertEquals('coffee_shop', $result['preset']->id);

        // Core capabilities remain enabled
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'pos'));
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'orders'));
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'billing'));

        // Preset-specific capabilities are enabled
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'venue.floors'));
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'venue.tables'));
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'venue.sessions'));
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'service.waiter'));
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'production.bar'));
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'inventory'));
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'recipes'));
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'menuza.menu'));
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'menuza.qr-ordering'));
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'loyalty'));

        // Non-preset capabilities remain disabled
        $this->assertFalse($this->capabilityManager->isEnabled($this->org, 'gaming'));
        $this->assertFalse($this->capabilityManager->isEnabled($this->org, 'production.shisha'));

        // Org settings record applied preset without erasing existing settings
        $refreshedOrg = $this->org->fresh();
        $this->assertEquals('coffee_shop', $refreshedOrg->settings['applied_preset']);
        $this->assertTrue($refreshedOrg->settings['initial_setup']);
    }

    public function test_can_apply_preset_to_specific_branch(): void
    {
        // Organization has full coffee_shop preset
        $this->presetService->apply($this->org, 'coffee_shop');

        // Branch 2 is an Express Kiosk; apply coffee_cart preset to Branch 2
        $result = $this->presetService->apply($this->org, 'coffee_cart', $this->branch2->id);

        $this->assertEquals('coffee_cart', $result['preset']->id);

        $refreshedBranch = $this->branch2->fresh();
        $this->assertEquals('coffee_cart', $refreshedBranch->settings['applied_preset']);

        // Branch 2 has bar and inventory enabled
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'production.bar', $this->branch2->id));
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'inventory', $this->branch2->id));
    }

    public function test_switching_presets_is_non_destructive_and_preserves_history(): void
    {
        // 1. Initial setup with Coffee Cart preset
        $this->presetService->apply($this->org, 'coffee_cart', null, [
            'inventory' => ['low_stock_threshold' => 10],
        ]);

        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'production.bar'));
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'inventory'));
        $this->assertFalse($this->capabilityManager->isEnabled($this->org, 'gaming'));

        $initialInventoryConfig = CapabilityConfiguration::where('organization_id', $this->org->id)
            ->where('capability_key', 'inventory')
            ->first();
        $this->assertEquals(10, $initialInventoryConfig->config['low_stock_threshold']);

        // 2. Business expands into Gaming Lounge: switch to Gaming Café preset
        $this->presetService->apply($this->org, 'gaming_cafe');

        // New gaming and timed resources capabilities are now enabled
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'timed-resources'));
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'gaming'));

        // Prior inventory capability is preserved and NOT wiped or corrupted
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'inventory'));
        $refreshedInventoryConfig = CapabilityConfiguration::where('organization_id', $this->org->id)
            ->where('capability_key', 'inventory')
            ->first();
        $this->assertEquals(10, $refreshedInventoryConfig->config['low_stock_threshold']);
        $this->assertEquals($initialInventoryConfig->id, $refreshedInventoryConfig->id);
    }

    public function test_disabling_preset_capability_preserves_audit_and_config_history(): void
    {
        // Apply traditional café preset
        $this->presetService->apply($this->org, 'traditional_cafe', null, [
            'production.shisha' => ['coal_burn_time_minutes' => 45],
        ]);

        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'production.shisha'));

        // Café temporarily suspends shisha service during municipal regulations review
        $disabled = $this->capabilityManager->disable(
            $this->org,
            'production.shisha',
            null,
            'Temporary municipal regulation suspension'
        );

        $this->assertFalse($this->capabilityManager->isEnabled($this->org, 'production.shisha'));
        $this->assertEquals(CapabilityConfiguration::STATUS_DISABLED, $disabled->status);
        $this->assertNotNull($disabled->disabled_at);
        $this->assertEquals('Temporary municipal regulation suspension', $disabled->disable_reason);
        $this->assertEquals(45, $disabled->config['coal_burn_time_minutes']);

        // Re-enabling shisha restores capability without losing prior settings
        $reEnabled = $this->capabilityManager->enable($this->org, 'production.shisha');
        $this->assertTrue($this->capabilityManager->isEnabled($this->org, 'production.shisha'));
        $this->assertEquals(CapabilityConfiguration::STATUS_ENABLED, $reEnabled->status);
        $this->assertEquals(45, $reEnabled->config['coal_burn_time_minutes']);
    }

    public function test_preset_does_not_fork_codebase_or_architecture(): void
    {
        // Validate that all presets operate on the unified CapabilityManager and identical database table
        $allPresets = $this->presetRegistry->all();
        $this->assertCount(8, $allPresets);

        foreach ($allPresets as $preset) {
            $this->assertNotEmpty($preset->capabilities);
            // Every capability must exist in the canonical registry
            foreach ($preset->capabilities as $capKey) {
                $this->assertTrue(
                    $this->capabilityManager->getRegistry()->has($capKey),
                    "Capability '{$capKey}' in preset '{$preset->id}' must exist in canonical registry."
                );
            }
        }
    }
}
