<?php

namespace App\Domain\Capability\Contracts;

class PresetDefinition
{
    /**
     * @param string $id Unique preset identifier (e.g. 'coffee_cart', 'gaming_cafe')
     * @param string $name Human-readable title
     * @param string $description Business overview of this café archetype
     * @param list<string> $capabilities Set of capabilities enabled by this preset
     * @param array<string, array<string, mixed>> $configurations Default configuration for specific capabilities
     * @param list<string> $recommendedSurfaces Default operational surfaces (pos, waiter, kds, admin)
     * @param array<string, mixed> $metadata Additional operational metadata (e.g. typical hardware setup)
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly array $capabilities,
        public readonly array $configurations = [],
        public readonly array $recommendedSurfaces = ['pos', 'admin'],
        public readonly array $metadata = [],
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'capabilities' => $this->capabilities,
            'configurations' => $this->configurations,
            'recommended_surfaces' => $this->recommendedSurfaces,
            'metadata' => $this->metadata,
        ];
    }
}
