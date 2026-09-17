<?php

namespace App\Domain\Capability\Contracts;

class CapabilityDefinition
{
    /**
     * @param string $key Unique machine-readable key (e.g. 'gaming', 'wifi', 'venue.tables')
     * @param string $family Capability family (e.g. 'core', 'venue', 'timed', 'production', 'wifi')
     * @param string $name Human-readable display name
     * @param string $description Detailed description of capability behavior
     * @param string $version Semantic version of capability contract
     * @param list<string> $dependencies Capability keys that must be enabled
     * @param list<string> $conflicts Capability keys that cannot be co-enabled
     * @param list<string> $supportedSurfaces Operational surfaces supported (pos, admin, waiter, kds, customer_display)
     * @param list<string> $requiredConfigKeys Keys in configuration that must be present and non-empty for 'enabled' status
     * @param array<string, mixed> $defaultConfig Default configuration values
     * @param bool $isCore Whether this is a core non-disableable capability
     */
    public function __construct(
        public readonly string $key,
        public readonly string $family,
        public readonly string $name,
        public readonly string $description,
        public readonly string $version = '1.0.0',
        public readonly array $dependencies = [],
        public readonly array $conflicts = [],
        public readonly array $supportedSurfaces = ['pos', 'admin'],
        public readonly array $requiredConfigKeys = [],
        public readonly array $defaultConfig = [],
        public readonly bool $isCore = false,
    ) {}

    /**
     * Validate whether given config fulfills required configuration keys.
     *
     * @param array<string, mixed> $config
     * @return list<string> List of missing required keys
     */
    public function getMissingConfigKeys(array $config): array
    {
        $missing = [];
        foreach ($this->requiredConfigKeys as $key) {
            if (!isset($config[$key]) || $config[$key] === '' || $config[$key] === null) {
                $missing[] = $key;
            }
        }
        return $missing;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'family' => $this->family,
            'name' => $this->name,
            'description' => $this->description,
            'version' => $this->version,
            'dependencies' => $this->dependencies,
            'conflicts' => $this->conflicts,
            'supported_surfaces' => $this->supportedSurfaces,
            'required_config_keys' => $this->requiredConfigKeys,
            'default_config' => $this->defaultConfig,
            'is_core' => $this->isCore,
        ];
    }
}
