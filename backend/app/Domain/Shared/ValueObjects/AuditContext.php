<?php

namespace App\Domain\Shared\ValueObjects;

use DateTimeInterface;
use Illuminate\Support\Carbon;
use JsonSerializable;

/**
 * Immutable AuditContext value object strictly conforming to AUDIT_EVENT_CONTRACT.md.
 */
class AuditContext implements JsonSerializable
{
    public const ACTOR_STAFF = 'staff';
    public const ACTOR_USER = 'user';
    public const ACTOR_DEVICE = 'device';
    public const ACTOR_SYSTEM = 'system';

    protected const SENSITIVE_KEYS = [
        'password',
        'password_confirmation',
        'pin',
        'pin_hash',
        'token',
        'token_hash',
        'device_token',
        'secret',
        'router_secret',
        'card_number',
        'cvv',
        'api_key',
    ];

    public function __construct(
        public readonly string $eventId,
        public readonly string $organizationId,
        public readonly ?string $branchId,
        public readonly string $actorId,
        public readonly string $actorType, // staff, user, device, system
        public readonly ?string $deviceId,
        public readonly string $actionKey,
        public readonly string $targetType,
        public readonly string $targetId,
        public readonly string $correlationId,
        public readonly ?string $reason = null,
        public readonly array $payload = [],
        public readonly ?string $ipAddress = null,
        public readonly ?string $userAgent = null,
        public readonly ?DateTimeInterface $occurredAt = null,
    ) {}

    /**
     * Create a new AuditContext with automatic UUIDv7 generation and sensitive payload redaction.
     */
    public static function create(
        string $organizationId,
        string $actionKey,
        string $targetType,
        string $targetId,
        string $actorId,
        string $actorType = self::ACTOR_STAFF,
        ?string $branchId = null,
        ?string $deviceId = null,
        ?string $correlationId = null,
        ?string $reason = null,
        array $payload = [],
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?DateTimeInterface $occurredAt = null
    ): self {
        return new self(
            eventId: CanonicalId::uuid7()->value(),
            organizationId: $organizationId,
            branchId: $branchId,
            actorId: $actorId,
            actorType: $actorType,
            deviceId: $deviceId,
            actionKey: $actionKey,
            targetType: $targetType,
            targetId: $targetId,
            correlationId: $correlationId ?: CanonicalId::uuid7()->value(),
            reason: $reason,
            payload: self::redactSensitiveData($payload),
            ipAddress: $ipAddress,
            userAgent: $userAgent,
            occurredAt: $occurredAt ?: Carbon::now('UTC')
        );
    }

    /**
     * Recursively scrub sensitive credentials (passwords, PINs, tokens) from payload.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function redactSensitiveData(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            $lowerKey = strtolower((string) $key);
            if (in_array($lowerKey, self::SENSITIVE_KEYS, true)) {
                $sanitized[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $sanitized[$key] = self::redactSensitiveData($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }

    public function jsonSerialize(): array
    {
        return [
            'event_id' => $this->eventId,
            'organization_id' => $this->organizationId,
            'branch_id' => $this->branchId,
            'actor_id' => $this->actorId,
            'actor_type' => $this->actorType,
            'device_id' => $this->deviceId,
            'action_key' => $this->actionKey,
            'target_type' => $this->targetType,
            'target_id' => $this->targetId,
            'correlation_id' => $this->correlationId,
            'reason' => $this->reason,
            'payload' => $this->payload,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'occurred_at' => ($this->occurredAt ?? Carbon::now('UTC'))->format(DateTimeInterface::ATOM),
        ];
    }
}
