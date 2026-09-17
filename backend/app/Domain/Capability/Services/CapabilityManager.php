<?php

namespace App\Domain\Capability\Services;

use App\Domain\Capability\Contracts\CapabilityDefinition;
use App\Domain\Capability\Exceptions\CapabilityDependencyException;
use App\Domain\Capability\Models\CapabilityConfiguration;
use App\Domain\Identity\Models\Organization;
use InvalidArgumentException;

class CapabilityManager
{
    public function __construct(
        protected CapabilityRegistry $registry
    ) {}

    public function getRegistry(): CapabilityRegistry
    {
        return $this->registry;
    }

    /**
     * Enable a capability for an organization or specific branch.
     *
     * @param Organization $org
     * @param string $key
     * @param string|null $branchId
     * @param array<string, mixed> $config
     * @param bool $atomic Enable all missing transitive dependencies automatically
     * @return CapabilityConfiguration
     */
    public function enable(
        Organization $org,
        string $key,
        ?string $branchId = null,
        array $config = [],
        bool $atomic = false
    ): CapabilityConfiguration {
        $def = $this->registry->get($key);
        if (!$def) {
            throw new InvalidArgumentException("Unknown capability '{$key}'.");
        }

        $activeKeys = $this->getActiveCapabilityKeys($org, $branchId);

        if ($atomic) {
            $atomicDeps = $this->registry->resolveAtomicDependencies($key, $activeKeys);
            foreach ($atomicDeps as $depKey) {
                $this->enable($org, $depKey, $branchId, [], false);
            }
            $activeKeys = $this->getActiveCapabilityKeys($org, $branchId);
        } else {
            $this->registry->validateCanEnable($key, $activeKeys);
        }

        $existing = CapabilityConfiguration::where('organization_id', $org->id)
            ->where('branch_id', $branchId)
            ->where('capability_key', $key)
            ->first();

        // Determine status based on required configuration (preserving existing config)
        $existingConfig = $existing ? ($existing->config ?? []) : [];
        $mergedConfig = array_merge($def->defaultConfig, $existingConfig, $config);
        $missingKeys = $def->getMissingConfigKeys($mergedConfig);

        $status = empty($missingKeys)
            ? CapabilityConfiguration::STATUS_ENABLED
            : CapabilityConfiguration::STATUS_NEEDS_CONFIGURATION;

        /** @var CapabilityConfiguration $configuration */
        $configuration = CapabilityConfiguration::updateOrCreate(
            [
                'organization_id' => $org->id,
                'branch_id' => $branchId,
                'capability_key' => $key,
            ],
            [
                'status' => $status,
                'config' => $mergedConfig,
                'version' => $def->version,
                'enabled_at' => now(),
                'disabled_at' => null,
                'disable_reason' => null,
            ]
        );

        return $configuration;
    }

    /**
     * Disable a capability for an organization or branch, preserving historical data.
     *
     * @param Organization $org
     * @param string $key
     * @param string|null $branchId
     * @param string|null $reason
     * @param bool $force Bypass active dependent check (will also cascade-disable dependents if needed)
     * @return CapabilityConfiguration
     */
    public function disable(
        Organization $org,
        string $key,
        ?string $branchId = null,
        ?string $reason = null,
        bool $force = false
    ): CapabilityConfiguration {
        $def = $this->registry->get($key);
        if (!$def) {
            throw new InvalidArgumentException("Unknown capability '{$key}'.");
        }

        if ($def->isCore) {
            throw new InvalidArgumentException("Core capability '{$key}' cannot be disabled.");
        }

        $activeKeys = $this->getActiveCapabilityKeys($org, $branchId);
        $dependents = $this->registry->findDependents($key, $activeKeys);

        if (!empty($dependents)) {
            if (!$force) {
                $depsList = implode(', ', $dependents);
                throw new CapabilityDependencyException(
                    $key,
                    $dependents,
                    "Cannot disable capability '{$key}': active dependent capabilities [{$depsList}] depend on it."
                );
            }

            // Force mode: cascade disable dependents
            foreach ($dependents as $depKey) {
                $this->disable($org, $depKey, $branchId, "Cascaded from disable of {$key}", true);
            }
        }

        /** @var CapabilityConfiguration $configuration */
        $configuration = CapabilityConfiguration::updateOrCreate(
            [
                'organization_id' => $org->id,
                'branch_id' => $branchId,
                'capability_key' => $key,
            ],
            [
                'status' => CapabilityConfiguration::STATUS_DISABLED,
                'disabled_at' => now(),
                'disable_reason' => $reason,
            ]
        );

        return $configuration;
    }

    /**
     * Check if a capability is enabled in this scope (inheriting organization setting if branch not overridden).
     */
    public function isEnabled(Organization $org, string $key, ?string $branchId = null): bool
    {
        $def = $this->registry->get($key);
        if ($def && $def->isCore) {
            return true;
        }

        // 1. Check branch override if branch provided
        if ($branchId !== null) {
            $branchConfig = CapabilityConfiguration::where('organization_id', $org->id)
                ->where('branch_id', $branchId)
                ->where('capability_key', $key)
                ->first();

            if ($branchConfig !== null) {
                return $branchConfig->isEnabled();
            }
        }

        // 2. Fall back to organization-wide configuration
        $orgConfig = CapabilityConfiguration::where('organization_id', $org->id)
            ->whereNull('branch_id')
            ->where('capability_key', $key)
            ->first();

        if ($orgConfig !== null) {
            return $orgConfig->isEnabled();
        }

        return false;
    }

    /**
     * Get effective status for a capability in this scope.
     */
    public function getStatus(Organization $org, string $key, ?string $branchId = null): string
    {
        $def = $this->registry->get($key);
        if ($def && $def->isCore) {
            return CapabilityConfiguration::STATUS_ENABLED;
        }

        if ($branchId !== null) {
            $branchConfig = CapabilityConfiguration::where('organization_id', $org->id)
                ->where('branch_id', $branchId)
                ->where('capability_key', $key)
                ->first();

            if ($branchConfig !== null) {
                return $branchConfig->status;
            }
        }

        $orgConfig = CapabilityConfiguration::where('organization_id', $org->id)
            ->whereNull('branch_id')
            ->where('capability_key', $key)
            ->first();

        if ($orgConfig !== null) {
            return $orgConfig->status;
        }

        return CapabilityConfiguration::STATUS_DISABLED;
    }

    /**
     * Retrieve all active (enabled or needs_configuration) capability keys in this scope.
     *
     * @return list<string>
     */
    public function getActiveCapabilityKeys(Organization $org, ?string $branchId = null): array
    {
        $keys = [];

        // All core capabilities are active by definition
        foreach ($this->registry->all() as $key => $def) {
            if ($def->isCore) {
                $keys[] = $key;
            }
        }

        // Org-level configurations
        $orgConfigs = CapabilityConfiguration::where('organization_id', $org->id)
            ->whereNull('branch_id')
            ->get()
            ->keyBy('capability_key');

        // Branch-level overrides
        $branchConfigs = $branchId !== null
            ? CapabilityConfiguration::where('organization_id', $org->id)
                ->where('branch_id', $branchId)
                ->get()
                ->keyBy('capability_key')
            : collect();

        foreach ($this->registry->all() as $key => $def) {
            if ($def->isCore) {
                continue;
            }

            // If branch override exists
            if ($branchConfigs->has($key)) {
                $cfg = $branchConfigs->get($key);
                if ($cfg->status !== CapabilityConfiguration::STATUS_DISABLED) {
                    $keys[] = $key;
                }
            } elseif ($orgConfigs->has($key)) {
                $cfg = $orgConfigs->get($key);
                if ($cfg->status !== CapabilityConfiguration::STATUS_DISABLED) {
                    $keys[] = $key;
                }
            }
        }

        return array_values(array_unique($keys));
    }

    /**
     * Get complete effective capability map for an organization / branch.
     *
     * @return array<string, array{status: string, config: array, is_override: bool}>
     */
    public function getEffectiveCapabilities(Organization $org, ?string $branchId = null): array
    {
        $result = [];

        $orgConfigs = CapabilityConfiguration::where('organization_id', $org->id)
            ->whereNull('branch_id')
            ->get()
            ->keyBy('capability_key');

        $branchConfigs = $branchId !== null
            ? CapabilityConfiguration::where('organization_id', $org->id)
                ->where('branch_id', $branchId)
                ->get()
                ->keyBy('capability_key')
            : collect();

        foreach ($this->registry->all() as $key => $def) {
            if ($def->isCore) {
                $result[$key] = [
                    'status' => CapabilityConfiguration::STATUS_ENABLED,
                    'config' => $def->defaultConfig,
                    'is_override' => false,
                ];
                continue;
            }

            if ($branchConfigs->has($key)) {
                $cfg = $branchConfigs->get($key);
                $result[$key] = [
                    'status' => $cfg->status,
                    'config' => $cfg->config ?? $def->defaultConfig,
                    'is_override' => true,
                ];
            } elseif ($orgConfigs->has($key)) {
                $cfg = $orgConfigs->get($key);
                $result[$key] = [
                    'status' => $cfg->status,
                    'config' => $cfg->config ?? $def->defaultConfig,
                    'is_override' => false,
                ];
            } else {
                $result[$key] = [
                    'status' => CapabilityConfiguration::STATUS_DISABLED,
                    'config' => $def->defaultConfig,
                    'is_override' => false,
                ];
            }
        }

        return $result;
    }
}
