<?php

namespace App\Domain\Capability\Services;

use App\Domain\Capability\Contracts\CapabilityDefinition;
use App\Domain\Capability\Exceptions\CapabilityConflictException;
use App\Domain\Capability\Exceptions\CapabilityDependencyException;
use InvalidArgumentException;

class CapabilityRegistry
{
    /** @var array<string, CapabilityDefinition> */
    protected array $capabilities = [];

    public function __construct()
    {
        $this->registerDefaults();
    }

    /**
     * Register a capability definition.
     */
    public function register(CapabilityDefinition $definition): void
    {
        $this->capabilities[$definition->key] = $definition;
    }

    /**
     * Retrieve a capability definition by key.
     */
    public function get(string $key): ?CapabilityDefinition
    {
        return $this->capabilities[$key] ?? null;
    }

    /**
     * Check if a capability is registered.
     */
    public function has(string $key): bool
    {
        return isset($this->capabilities[$key]);
    }

    /**
     * Retrieve all registered capabilities.
     *
     * @return array<string, CapabilityDefinition>
     */
    public function all(): array
    {
        return $this->capabilities;
    }

    /**
     * Retrieve capabilities by family.
     *
     * @return array<string, CapabilityDefinition>
     */
    public function byFamily(string $family): array
    {
        return array_filter($this->capabilities, fn (CapabilityDefinition $c) => $c->family === $family);
    }

    /**
     * Validate dependencies and conflicts for enabling a capability given currently enabled keys.
     *
     * @param string $key
     * @param list<string> $currentlyEnabledKeys
     * @throws CapabilityDependencyException
     * @throws CapabilityConflictException
     */
    public function validateCanEnable(string $key, array $currentlyEnabledKeys): void
    {
        $def = $this->get($key);
        if (!$def) {
            throw new InvalidArgumentException("Unknown capability '{$key}'.");
        }

        // 1. Check missing dependencies
        $missing = [];
        foreach ($def->dependencies as $dep) {
            if (!in_array($dep, $currentlyEnabledKeys, true)) {
                $missing[] = $dep;
            }
        }

        if (!empty($missing)) {
            throw new CapabilityDependencyException($key, $missing);
        }

        // 2. Check conflicts
        $conflicts = [];
        foreach ($def->conflicts as $conflictKey) {
            if (in_array($conflictKey, $currentlyEnabledKeys, true)) {
                $conflicts[] = $conflictKey;
            }
        }

        if (!empty($conflicts)) {
            throw new CapabilityConflictException($key, $conflicts);
        }
    }

    /**
     * Calculate all transitive dependencies required to enable a capability.
     *
     * @param string $key
     * @param list<string> $currentlyEnabledKeys
     * @return list<string> Ordered list of dependencies to enable atomically (excluding already enabled)
     */
    public function resolveAtomicDependencies(string $key, array $currentlyEnabledKeys): array
    {
        $def = $this->get($key);
        if (!$def) {
            throw new InvalidArgumentException("Unknown capability '{$key}'.");
        }

        $needed = [];
        $visited = [];

        $traverse = function (string $currentKey) use (&$traverse, &$needed, &$visited, $currentlyEnabledKeys) {
            if (isset($visited[$currentKey])) {
                return;
            }
            $visited[$currentKey] = true;

            $currentDef = $this->get($currentKey);
            if (!$currentDef) {
                return;
            }

            foreach ($currentDef->dependencies as $dep) {
                $traverse($dep);
                if (!in_array($dep, $currentlyEnabledKeys, true) && !in_array($dep, $needed, true)) {
                    $needed[] = $dep;
                }
            }
        };

        $traverse($key);

        return $needed;
    }

    /**
     * Find all active capabilities that directly depend on a given capability.
     *
     * @param string $key
     * @param list<string> $currentlyEnabledKeys
     * @return list<string>
     */
    public function findDependents(string $key, array $currentlyEnabledKeys): array
    {
        $dependents = [];
        foreach ($currentlyEnabledKeys as $activeKey) {
            if ($activeKey === $key) {
                continue;
            }
            $activeDef = $this->get($activeKey);
            if ($activeDef && in_array($key, $activeDef->dependencies, true)) {
                $dependents[] = $activeKey;
            }
        }
        return $dependents;
    }

    /**
     * Register standard Bunova canonical capabilities.
     */
    protected function registerDefaults(): void
    {
        // Core (Always enabled foundation)
        $coreCapabilities = [
            'core.organization' => 'Tenant organization and company primitives',
            'core.branch' => 'Physical branch venue primitives',
            'core.device' => 'Registered POS/KDS operational hardware devices',
            'core.identity' => 'Identity, users, staff, and PIN authentication',
            'core.permissions' => 'Granular roles and branch-scoped policies',
            'catalog' => 'Menu catalog, categories, products, variants, modifiers',
            'pos' => 'Front-of-house point-of-sale checkout and register operation',
            'orders' => 'Order state machine, items, tickets, and fulfillment lifecycle',
            'billing' => 'Unified customer billing engine, calculations, splits, taxes',
            'payments' => 'Tender processing, electronic card, cash, refunds, ledger',
            'cash-shifts' => 'Cash drawer shifts, float count, drop, variance audit',
            'audit' => 'Immutable audit trail per AUDIT_EVENT_CONTRACT.md',
            'reporting' => 'Operational sales, shift, product, and staff performance reports',
        ];

        foreach ($coreCapabilities as $key => $desc) {
            $this->register(new CapabilityDefinition(
                key: $key,
                family: 'core',
                name: ucwords(str_replace(['.', '-'], ' ', $key)),
                description: $desc,
                isCore: true
            ));
        }

        // Venue & Floor
        $this->register(new CapabilityDefinition(
            key: 'venue.floors',
            family: 'venue',
            name: 'Floor Layouts',
            description: 'Physical floor plans, zones, indoor/outdoor areas',
            dependencies: ['core.branch']
        ));

        $this->register(new CapabilityDefinition(
            key: 'venue.tables',
            family: 'venue',
            name: 'Table Management',
            description: 'Table layout, seating capacity, table status tracking',
            dependencies: ['venue.floors']
        ));

        $this->register(new CapabilityDefinition(
            key: 'venue.rooms',
            family: 'venue',
            name: 'Private Rooms',
            description: 'Private rooms, meeting spaces, VIP booths',
            dependencies: ['venue.floors']
        ));

        $this->register(new CapabilityDefinition(
            key: 'venue.sessions',
            family: 'venue',
            name: 'Venue Service Sessions',
            description: 'Customer dining/service session lifecycle with open tabs',
            dependencies: ['orders', 'billing']
        ));

        // Service
        $this->register(new CapabilityDefinition(
            key: 'service.waiter',
            family: 'service',
            name: 'Waiter Handheld Service',
            description: 'Waitstaff table-side ordering on handheld devices',
            dependencies: ['orders', 'venue.tables']
        ));

        // Production Stations
        $this->register(new CapabilityDefinition(
            key: 'production.bar',
            family: 'production',
            name: 'Bar Station Routing',
            description: 'Coffee, espresso, beverage station ticket routing',
            dependencies: ['orders']
        ));

        $this->register(new CapabilityDefinition(
            key: 'production.kitchen',
            family: 'production',
            name: 'Kitchen Station Routing',
            description: 'Food preparation station ticket routing',
            dependencies: ['orders']
        ));

        $this->register(new CapabilityDefinition(
            key: 'production.shisha',
            family: 'production',
            name: 'Shisha Service Station',
            description: 'Shisha preparation, coal maintenance, station routing',
            dependencies: ['orders']
        ));

        $this->register(new CapabilityDefinition(
            key: 'production.kds',
            family: 'production',
            name: 'Kitchen Display System (KDS)',
            description: 'Digital display bump bar screens replacing paper tickets',
            dependencies: ['orders']
        ));

        // Timed Resources & Gaming
        $this->register(new CapabilityDefinition(
            key: 'timed-resources',
            family: 'timed',
            name: 'Timed Resources Engine',
            description: 'Hourly/rate-based billing engine for consoles, tables, workspaces',
            dependencies: ['billing', 'orders']
        ));

        $this->register(new CapabilityDefinition(
            key: 'gaming',
            family: 'gaming',
            name: 'Gaming Lounge & Consoles',
            description: 'PlayStation, Xbox, PC gaming session tracking with controller counting',
            dependencies: ['timed-resources', 'billing', 'venue.sessions']
        ));

        // Wi-Fi
        $this->register(new CapabilityDefinition(
            key: 'wifi',
            family: 'wifi',
            name: 'Wi-Fi Hotspot & Voucher OS',
            description: 'MikroTik router integration with automated guest Wi-Fi voucher issuance',
            dependencies: ['billing'],
            requiredConfigKeys: ['router_host', 'router_secret']
        ));

        // Inventory, Recipes & Procurement
        $this->register(new CapabilityDefinition(
            key: 'inventory',
            family: 'inventory',
            name: 'Stock Inventory Ledger',
            description: 'Ingredient and product stock balances, unit conversions, movements',
            dependencies: ['catalog']
        ));

        $this->register(new CapabilityDefinition(
            key: 'recipes',
            family: 'inventory',
            name: 'Recipes & Bill of Materials',
            description: 'Menu item recipe breakdown with automatic ingredient stock depletion',
            dependencies: ['inventory', 'catalog']
        ));

        $this->register(new CapabilityDefinition(
            key: 'procurement',
            family: 'inventory',
            name: 'Supplier Procurement',
            description: 'Supplier accounts, purchase orders, goods receiving, operational dues',
            dependencies: ['inventory']
        ));

        $this->register(new CapabilityDefinition(
            key: 'waste',
            family: 'inventory',
            name: 'Waste & Spoilage Tracking',
            description: 'Waste recording, spoilage write-offs, staff consumption tracking',
            dependencies: ['inventory']
        ));

        // Menuza Integration
        $this->register(new CapabilityDefinition(
            key: 'menuza.menu',
            family: 'menuza',
            name: 'Menuza Digital Menu',
            description: 'Catalog publication to Menuza digital QR menu',
            dependencies: ['catalog']
        ));

        $this->register(new CapabilityDefinition(
            key: 'menuza.qr-ordering',
            family: 'menuza',
            name: 'Menuza Table QR Ordering',
            description: 'Customer self-ordering from table QR code directly into POS queue',
            dependencies: ['menuza.menu', 'venue.tables', 'orders']
        ));

        $this->register(new CapabilityDefinition(
            key: 'menuza.online-ordering',
            family: 'menuza',
            name: 'Menuza Pickup & Online Orders',
            description: 'Customer online pickup and delivery ordering',
            dependencies: ['menuza.menu', 'orders']
        ));

        // Reservations
        $this->register(new CapabilityDefinition(
            key: 'reservations',
            family: 'venue',
            name: 'Reservations Management',
            description: 'Advance booking for dining tables, rooms, and gaming stations',
            dependencies: ['venue.floors']
        ));

        // Loyalty & Memberships
        $this->register(new CapabilityDefinition(
            key: 'loyalty',
            family: 'customer',
            name: 'Customer Loyalty & Points',
            description: 'Points accumulation, reward redemptions, customer tiers',
            dependencies: ['billing', 'orders']
        ));

        $this->register(new CapabilityDefinition(
            key: 'memberships',
            family: 'customer',
            name: 'Customer Memberships & Passes',
            description: 'Prepaid membership cards, recurring time passes, discounts',
            dependencies: ['billing', 'loyalty']
        ));

        // Fiscal
        $this->register(new CapabilityDefinition(
            key: 'fiscal.egypt',
            family: 'fiscal',
            name: 'Egypt ETA eReceipt Fiscal Authority',
            description: 'Automated integration with Egyptian Tax Authority eReceipt portal',
            dependencies: ['billing', 'payments'],
            requiredConfigKeys: ['tax_id', 'pos_serial']
        ));

        // Analytics
        $this->register(new CapabilityDefinition(
            key: 'analytics.advanced',
            family: 'analytics',
            name: 'Advanced Analytics & Forecasting',
            description: 'Advanced business intelligence, consolidated multi-branch reporting, forecasting',
            dependencies: ['reporting']
        ));
    }
}
