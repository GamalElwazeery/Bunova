<?php

namespace App\Domain\Capability\Services;

use App\Domain\Capability\Contracts\PresetDefinition;

class PresetRegistry
{
    /** @var array<string, PresetDefinition> */
    protected array $presets = [];

    public function __construct()
    {
        $this->registerPresets();
    }

    public function register(PresetDefinition $preset): void
    {
        $this->presets[$preset->id] = $preset;
    }

    public function get(string $id): ?PresetDefinition
    {
        return $this->presets[$id] ?? null;
    }

    public function has(string $id): bool
    {
        return isset($this->presets[$id]);
    }

    /**
     * @return array<string, PresetDefinition>
     */
    public function all(): array
    {
        return $this->presets;
    }

    protected function registerPresets(): void
    {
        // 1. Coffee Cart / Mobile Kiosk
        $this->register(new PresetDefinition(
            id: 'coffee_cart',
            name: 'Coffee Cart / Mobile Kiosk',
            description: 'Minimal footprint mobile cart or kiosk focusing on fast POS checkout, espresso bar production, basic stock, and offline resilience.',
            capabilities: [
                'production.bar',
                'inventory',
            ],
            recommendedSurfaces: ['pos'],
            metadata: ['layout' => 'compact_pos', 'typical_devices' => 1]
        ));

        // 2. Takeaway Coffee Kiosk
        $this->register(new PresetDefinition(
            id: 'takeaway_kiosk',
            name: 'Takeaway Coffee Kiosk',
            description: 'High-volume express takeaway location with bar production routing, recipes BOM ingredient tracking, and digital QR menu.',
            capabilities: [
                'production.bar',
                'inventory',
                'recipes',
                'menuza.menu',
            ],
            recommendedSurfaces: ['pos', 'admin'],
            metadata: ['layout' => 'quick_service', 'typical_devices' => 2]
        ));

        // 3. Specialty Coffee Shop
        $this->register(new PresetDefinition(
            id: 'coffee_shop',
            name: 'Specialty Coffee Shop',
            description: 'Dine-in specialty coffee venue with table seating, open tabs, handheld waiter ordering, bar routing, recipes, Menuza QR ordering, and customer loyalty.',
            capabilities: [
                'venue.floors',
                'venue.tables',
                'venue.sessions',
                'service.waiter',
                'production.bar',
                'inventory',
                'recipes',
                'menuza.menu',
                'menuza.qr-ordering',
                'loyalty',
            ],
            recommendedSurfaces: ['pos', 'waiter', 'admin'],
            metadata: ['layout' => 'table_service', 'typical_devices' => 4]
        ));

        // 4. Traditional Café
        $this->register(new PresetDefinition(
            id: 'traditional_cafe',
            name: 'Traditional Café (Ahwa)',
            description: 'Classic social café with open-ended session tabs, dedicated shisha preparation routing, hot drinks bar, kitchen snacks, and customer Wi-Fi.',
            capabilities: [
                'venue.floors',
                'venue.tables',
                'venue.sessions',
                'service.waiter',
                'production.bar',
                'production.kitchen',
                'production.shisha',
                'wifi',
                'inventory',
                'recipes',
                'menuza.menu',
                'menuza.qr-ordering',
            ],
            recommendedSurfaces: ['pos', 'waiter', 'admin'],
            metadata: ['layout' => 'session_heavy', 'typical_devices' => 5]
        ));

        // 5. Café & Casual Restaurant
        $this->register(new PresetDefinition(
            id: 'cafe_and_restaurant',
            name: 'Café & Casual Restaurant',
            description: 'Hybrid dining venue with bar & kitchen production, Kitchen Display System (KDS), table reservations, comprehensive inventory procurement, and waste management.',
            capabilities: [
                'venue.floors',
                'venue.tables',
                'venue.rooms',
                'venue.sessions',
                'service.waiter',
                'production.bar',
                'production.kitchen',
                'production.kds',
                'reservations',
                'inventory',
                'recipes',
                'procurement',
                'waste',
                'menuza.menu',
                'menuza.qr-ordering',
                'loyalty',
            ],
            recommendedSurfaces: ['pos', 'waiter', 'kds', 'admin'],
            metadata: ['layout' => 'full_restaurant', 'typical_devices' => 8]
        ));

        // 6. Gaming Café & Lounge
        $this->register(new PresetDefinition(
            id: 'gaming_cafe',
            name: 'Gaming Café & Lounge',
            description: 'PlayStation/Xbox/PC gaming venue with timed station hourly billing, console/controller session tracking, bar snacks, customer Wi-Fi, and membership passes.',
            capabilities: [
                'venue.floors',
                'venue.rooms',
                'venue.sessions',
                'timed-resources',
                'gaming',
                'production.bar',
                'wifi',
                'inventory',
                'memberships',
                'reservations',
            ],
            recommendedSurfaces: ['pos', 'admin'],
            metadata: ['layout' => 'gaming_lounge', 'typical_devices' => 4]
        ));

        // 7. Internet & Coworking Café
        $this->register(new PresetDefinition(
            id: 'coworking_cafe',
            name: 'Internet & Coworking Café',
            description: 'Shared workspace and study café with hot desks, meeting rooms, hourly workspace passes, Wi-Fi voucher integration, beverage bar, and advance room reservations.',
            capabilities: [
                'venue.floors',
                'venue.tables',
                'venue.rooms',
                'venue.sessions',
                'timed-resources',
                'wifi',
                'production.bar',
                'memberships',
                'reservations',
            ],
            recommendedSurfaces: ['pos', 'admin'],
            metadata: ['layout' => 'coworking', 'typical_devices' => 3]
        ));

        // 8. Multi-Branch Chain
        $this->register(new PresetDefinition(
            id: 'multi_branch_chain',
            name: 'Multi-Branch Café Chain',
            description: 'Multi-unit operator with centralized catalog, kitchen/bar operations, procurement ledger, and advanced cross-branch consolidated analytics.',
            capabilities: [
                'venue.floors',
                'venue.tables',
                'venue.sessions',
                'service.waiter',
                'production.bar',
                'production.kitchen',
                'inventory',
                'recipes',
                'procurement',
                'analytics.advanced',
            ],
            recommendedSurfaces: ['pos', 'waiter', 'admin'],
            metadata: ['layout' => 'enterprise_chain', 'typical_devices' => 12]
        ));
    }
}
