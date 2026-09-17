<?php

namespace App\Domain\Capability\Exceptions;

use RuntimeException;

class CapabilityDependencyException extends RuntimeException
{
    /**
     * @param string $capabilityKey
     * @param list<string> $missingDependencies
     */
    public function __construct(
        public readonly string $capabilityKey,
        public readonly array $missingDependencies,
        string $message = ''
    ) {
        $deps = implode(', ', $missingDependencies);
        $msg = $message ?: "Cannot enable capability '{$capabilityKey}': missing required dependencies [{$deps}].";
        parent::__construct($msg);
    }
}
