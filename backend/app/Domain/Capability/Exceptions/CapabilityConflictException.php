<?php

namespace App\Domain\Capability\Exceptions;

use RuntimeException;

class CapabilityConflictException extends RuntimeException
{
    /**
     * @param string $capabilityKey
     * @param list<string> $conflictingKeys
     */
    public function __construct(
        public readonly string $capabilityKey,
        public readonly array $conflictingKeys,
        string $message = ''
    ) {
        $conflicts = implode(', ', $conflictingKeys);
        $msg = $message ?: "Cannot enable capability '{$capabilityKey}': conflicting capabilities [{$conflicts}] are active.";
        parent::__construct($msg);
    }
}
