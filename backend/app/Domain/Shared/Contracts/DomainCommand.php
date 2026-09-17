<?php

namespace App\Domain\Shared\Contracts;

use App\Domain\Shared\ValueObjects\CanonicalId;

abstract class DomainCommand
{
    public readonly string $commandId;
    public readonly string $organizationId;
    public readonly ?string $branchId;
    public readonly string $actorId;
    public readonly string $actorType; // staff, user, device, system
    public readonly string $correlationId;

    public function __construct(
        string $organizationId,
        string $actorId,
        string $actorType = 'staff',
        ?string $branchId = null,
        ?string $correlationId = null,
        ?string $commandId = null
    ) {
        $this->commandId = $commandId ?: CanonicalId::uuid7()->value();
        $this->organizationId = $organizationId;
        $this->branchId = $branchId;
        $this->actorId = $actorId;
        $this->actorType = $actorType;
        $this->correlationId = $correlationId ?: CanonicalId::uuid7()->value();
    }
}
