<?php

namespace App\Domain\Shared\ValueObjects;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Stringable;

class CanonicalId implements Stringable
{
    protected string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            throw new InvalidArgumentException('ID cannot be empty.');
        }
        $this->value = $trimmed;
    }

    /**
     * Generate a sortable, time-ordered UUIDv7.
     */
    public static function uuid7(): self
    {
        return new self((string) Str::uuid7());
    }

    /**
     * Generate a sortable ULID.
     */
    public static function ulid(): self
    {
        return new self((string) Str::ulid());
    }

    /**
     * Generate a prefixed identifier with UUIDv7 (e.g. 'ord_018f...').
     */
    public static function prefixed(string $prefix): self
    {
        return new self($prefix . '_' . (string) Str::uuid7());
    }

    /**
     * Validate whether a string is a valid UUID (v4 or v7).
     */
    public static function isValidUuid(string $value): bool
    {
        return Str::isUuid($value);
    }

    /**
     * Validate whether a string is a valid ULID.
     */
    public static function isValidUlid(string $value): bool
    {
        return Str::isUlid($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(CanonicalId|string $other): bool
    {
        $otherVal = $other instanceof self ? $other->value() : $other;
        return $this->value === $otherVal;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
