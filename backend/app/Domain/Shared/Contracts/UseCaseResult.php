<?php

namespace App\Domain\Shared\Contracts;

class UseCaseResult
{
    /**
     * @param bool $success
     * @param mixed $data
     * @param string|null $message
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        public readonly bool $success,
        public readonly mixed $data = null,
        public readonly ?string $message = null,
        public readonly array $metadata = []
    ) {}

    public static function ok(mixed $data = null, ?string $message = null, array $metadata = []): self
    {
        return new self(true, $data, $message, $metadata);
    }

    public static function fail(string $message, mixed $data = null, array $metadata = []): self
    {
        return new self(false, $data, $message, $metadata);
    }
}
