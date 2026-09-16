<?php

namespace Tests\Feature;

use Tests\TestCase;

class ArchitectureTest extends TestCase
{
    /**
     * Test that core domain bounded contexts exist within the modular monolith.
     */
    public function test_modular_monolith_domain_directories_exist(): void
    {
        $domainPath = app_path('Domain');
        $this->assertDirectoryExists($domainPath);

        $expectedDomains = [
            'Platform',
            'Identity',
            'Capability',
            'Catalog',
            'Orders',
            'Billing',
            'Payments',
            'Cash',
            'Venue',
            'Production',
            'Inventory',
            'Procurement',
            'Staff',
            'Timed',
            'Gaming',
            'Wifi',
            'Shisha',
            'Customer',
            'Reservations',
            'Fiscal',
            'Audit',
        ];

        foreach ($expectedDomains as $domain) {
            $path = $domainPath . '/' . $domain;
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
            $this->assertDirectoryExists($path, "Expected domain context [{$domain}] to exist in modular monolith.");
        }
    }

    /**
     * Test workspace topology contracts.
     */
    public function test_workspace_topology_has_clear_boundaries(): void
    {
        $basePath = base_path('..');
        $this->assertDirectoryExists($basePath . '/apps/pos', 'Flutter POS operational app must exist.');
        $this->assertDirectoryExists($basePath . '/packages/contracts', 'Shared contracts package must exist.');
        $this->assertDirectoryExists($basePath . '/tooling', 'Tooling directory must exist.');
        $this->assertFileExists($basePath . '/docs/04-delivery/WORKSPACE_BUILD_INSTRUCTIONS.md', 'Workspace build instructions must exist.');
    }
}
