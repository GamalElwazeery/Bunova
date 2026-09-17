<?php

namespace App\Domain\Capability\Services;

use App\Domain\Capability\Contracts\PresetDefinition;
use App\Domain\Identity\Models\Branch;
use App\Domain\Identity\Models\Organization;
use InvalidArgumentException;

class PresetService
{
    public function __construct(
        protected PresetRegistry $presetRegistry,
        protected CapabilityManager $capabilityManager
    ) {}

    public function getPresetRegistry(): PresetRegistry
    {
        return $this->presetRegistry;
    }

    /**
     * Apply a café archetype preset to an organization or branch non-destructively.
     *
     * @param Organization $org
     * @param string $presetId
     * @param string|null $branchId
     * @param array<string, array<string, mixed>> $configOverrides
     * @return array{preset: PresetDefinition, enabled_capabilities: list<string>}
     */
    public function apply(
        Organization $org,
        string $presetId,
        ?string $branchId = null,
        array $configOverrides = []
    ): array {
        $preset = $this->presetRegistry->get($presetId);
        if (!$preset) {
            throw new InvalidArgumentException("Unknown onboarding preset '{$presetId}'.");
        }

        $enabledKeys = [];

        foreach ($preset->capabilities as $capKey) {
            $config = array_merge(
                $preset->configurations[$capKey] ?? [],
                $configOverrides[$capKey] ?? []
            );

            // Enable capability with atomic dependency resolution
            $this->capabilityManager->enable(
                $org,
                $capKey,
                $branchId,
                $config,
                atomic: true
            );

            $enabledKeys[] = $capKey;
        }

        // Record applied preset metadata in settings without erasing other settings
        if ($branchId !== null) {
            $branch = Branch::where('id', $branchId)->where('organization_id', $org->id)->first();
            if ($branch) {
                $settings = $branch->settings ?? [];
                $settings['applied_preset'] = $presetId;
                $settings['preset_applied_at'] = now()->toIso8601String();
                $branch->update(['settings' => $settings]);
            }
        } else {
            $settings = $org->settings ?? [];
            $settings['applied_preset'] = $presetId;
            $settings['preset_applied_at'] = now()->toIso8601String();
            $org->update(['settings' => $settings]);
        }

        return [
            'preset' => $preset,
            'enabled_capabilities' => $enabledKeys,
        ];
    }

    /**
     * Validate that all registered presets have valid, resolvable dependency graphs.
     *
     * @return array<string, list<string>> Map of preset ID to list of validation errors
     */
    public function validateAllPresets(): array
    {
        $errors = [];
        $registry = $this->capabilityManager->getRegistry();

        // Get core capability keys
        $coreKeys = [];
        foreach ($registry->all() as $k => $def) {
            if ($def->isCore) {
                $coreKeys[] = $k;
            }
        }

        foreach ($this->presetRegistry->all() as $presetId => $preset) {
            $presetErrors = [];
            $allKeys = array_unique(array_merge($coreKeys, $preset->capabilities));

            // Check each capability exists in capability registry
            foreach ($preset->capabilities as $capKey) {
                if (!$registry->has($capKey)) {
                    $presetErrors[] = "Preset references unregistered capability '{$capKey}'.";
                    continue;
                }

                $def = $registry->get($capKey);

                // Verify dependencies are present in preset or core, or can be resolved atomically
                foreach ($def->dependencies as $dep) {
                    if (!in_array($dep, $allKeys, true)) {
                        // Check if it can be resolved atomically
                        $atomic = $registry->resolveAtomicDependencies($capKey, $allKeys);
                        if (empty($atomic)) {
                            $presetErrors[] = "Capability '{$capKey}' is missing required dependency '{$dep}'.";
                        }
                    }
                }

                // Verify conflicts
                foreach ($def->conflicts as $conflict) {
                    if (in_array($conflict, $allKeys, true)) {
                        $presetErrors[] = "Capability '{$capKey}' conflicts with '{$conflict}'.";
                    }
                }
            }

            if (!empty($presetErrors)) {
                $errors[$presetId] = $presetErrors;
            }
        }

        return $errors;
    }
}
