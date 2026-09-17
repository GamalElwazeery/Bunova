<?php

namespace App\Domain\Identity\Services;

use App\Domain\Identity\Models\Branch;
use App\Domain\Identity\Models\RegisteredDevice;
use App\Domain\Shared\Models\AuditEvent;
use App\Domain\Shared\Services\AuditService;
use Illuminate\Support\Str;
use InvalidArgumentException;

class DeviceRegistryService
{
    public const TOKEN_PREFIX = 'bnd_';

    public function __construct(
        protected ?AuditService $auditService = null
    ) {
        $this->auditService = $this->auditService ?? app(AuditService::class);
    }

    /**
     * Enroll a new physical operational device.
     *
     * @param array{
     *     organization_id: string,
     *     branch_id: string,
     *     device_code: string,
     *     name: string,
     *     device_type?: string,
     *     capabilities?: list<string>,
     *     hardware_metadata?: array,
     *     settings?: array
     * } $data
     * @return array{device: RegisteredDevice, token: string}
     */
    public function enroll(array $data): array
    {
        // 1. Verify branch belongs to organization
        $branch = Branch::where('id', $data['branch_id'])
            ->where('organization_id', $data['organization_id'])
            ->first();

        if (!$branch) {
            throw new InvalidArgumentException("Invalid branch or organization association.");
        }

        // 2. Ensure device_code is unique within this branch
        $exists = RegisteredDevice::where('organization_id', $data['organization_id'])
            ->where('branch_id', $data['branch_id'])
            ->where('device_code', $data['device_code'])
            ->exists();

        if ($exists) {
            throw new InvalidArgumentException("Device code '{$data['device_code']}' already exists in this branch.");
        }

        // 3. Generate high-entropy credential token
        $rawSecret = bin2hex(random_bytes(32));
        $token = self::TOKEN_PREFIX . $rawSecret;
        $tokenHash = hash('sha256', $token);
        $prefix = substr($token, 0, 12);

        // 4. Create RegisteredDevice record
        $device = RegisteredDevice::create([
            'organization_id' => $data['organization_id'],
            'branch_id' => $data['branch_id'],
            'device_code' => $data['device_code'],
            'name' => $data['name'],
            'device_type' => $data['device_type'] ?? RegisteredDevice::TYPE_POS_REGISTER,
            'status' => RegisteredDevice::STATUS_ACTIVE,
            'api_key_prefix' => $prefix,
            'token_hash' => $tokenHash,
            'capabilities' => $data['capabilities'] ?? [
                RegisteredDevice::CAPABILITY_RECEIPT_PRINTING,
                RegisteredDevice::CAPABILITY_CASH_DRAWER,
            ],
            'hardware_metadata' => $data['hardware_metadata'] ?? [],
            'settings' => $data['settings'] ?? [],
            'registered_at' => now(),
            'last_seen_at' => now(),
        ]);

        // Record audit event for device enrollment
        $this->auditService->recordSensitiveAction(
            organizationId: $device->organization_id,
            actionKey: AuditEvent::ACTION_DEVICE_REGISTERED,
            targetType: 'RegisteredDevice',
            targetId: $device->id,
            reason: 'Device enrollment',
            before: [],
            after: [
                'device_code' => $device->device_code,
                'device_type' => $device->device_type,
                'status' => $device->status,
            ],
            branchId: $device->branch_id,
            deviceId: $device->id
        );

        return [
            'device' => $device,
            'token' => $token,
        ];
    }

    /**
     * Authenticate a device credential token.
     */
    public function authenticate(string $token): ?RegisteredDevice
    {
        if (!str_starts_with($token, self::TOKEN_PREFIX)) {
            return null;
        }

        $hash = hash('sha256', $token);

        /** @var RegisteredDevice|null $device */
        $device = RegisteredDevice::where('token_hash', $hash)->first();

        if (!$device) {
            return null;
        }

        // Disallow revoked or suspended devices
        if (!$device->isActive()) {
            return null;
        }

        // Update last seen timestamp
        $device->update(['last_seen_at' => now()]);

        return $device;
    }

    /**
     * Revoke a device permanently.
     */
    public function revoke(RegisteredDevice $device, string $reason): void
    {
        $before = ['status' => $device->status, 'token_hash' => $device->token_hash];

        $device->update([
            'status' => RegisteredDevice::STATUS_REVOKED,
            'token_hash' => null, // Scramble/clear token to ensure credentials cannot be reused
            'revoked_at' => now(),
            'revocation_reason' => $reason,
        ]);

        $this->auditService->recordSensitiveAction(
            organizationId: $device->organization_id,
            actionKey: AuditEvent::ACTION_DEVICE_REVOKED,
            targetType: 'RegisteredDevice',
            targetId: $device->id,
            reason: $reason,
            before: $before,
            after: ['status' => RegisteredDevice::STATUS_REVOKED, 'token_hash' => null],
            branchId: $device->branch_id,
            deviceId: $device->id
        );
    }

    /**
     * Suspend a device temporarily.
     */
    public function suspend(RegisteredDevice $device, string $reason): void
    {
        $before = ['status' => $device->status];

        $device->update([
            'status' => RegisteredDevice::STATUS_SUSPENDED,
            'revocation_reason' => $reason,
        ]);

        $this->auditService->recordSensitiveAction(
            organizationId: $device->organization_id,
            actionKey: AuditEvent::ACTION_AUTH_SECURITY,
            targetType: 'RegisteredDevice',
            targetId: $device->id,
            reason: $reason,
            before: $before,
            after: ['status' => RegisteredDevice::STATUS_SUSPENDED],
            branchId: $device->branch_id,
            deviceId: $device->id
        );
    }

    /**
     * Reactivate a suspended device.
     */
    public function reactivate(RegisteredDevice $device): void
    {
        if ($device->isRevoked()) {
            throw new InvalidArgumentException("Revoked devices cannot be reactivated; re-enrollment is required.");
        }

        $before = ['status' => $device->status];

        $device->update([
            'status' => RegisteredDevice::STATUS_ACTIVE,
            'revocation_reason' => null,
        ]);

        $this->auditService->recordSensitiveAction(
            organizationId: $device->organization_id,
            actionKey: AuditEvent::ACTION_AUTH_SECURITY,
            targetType: 'RegisteredDevice',
            targetId: $device->id,
            reason: 'Device reactivated from suspension',
            before: $before,
            after: ['status' => RegisteredDevice::STATUS_ACTIVE],
            branchId: $device->branch_id,
            deviceId: $device->id
        );
    }

    /**
     * Rotate credentials for an active device.
     */
    public function rotateCredentials(RegisteredDevice $device): string
    {
        if (!$device->isActive()) {
            throw new InvalidArgumentException("Cannot rotate credentials for a non-active device.");
        }

        $rawSecret = bin2hex(random_bytes(32));
        $newToken = self::TOKEN_PREFIX . $rawSecret;
        $tokenHash = hash('sha256', $newToken);
        $prefix = substr($newToken, 0, 12);

        $device->update([
            'api_key_prefix' => $prefix,
            'token_hash' => $tokenHash,
        ]);

        $this->auditService->recordSensitiveAction(
            organizationId: $device->organization_id,
            actionKey: AuditEvent::ACTION_AUTH_SECURITY,
            targetType: 'RegisteredDevice',
            targetId: $device->id,
            reason: 'Device credential rotation',
            before: ['api_key_prefix' => $device->api_key_prefix],
            after: ['api_key_prefix' => $prefix],
            branchId: $device->branch_id,
            deviceId: $device->id
        );

        return $newToken;
    }

    /**
     * Record device heartbeat and optional updated telemetry/hardware metadata.
     */
    public function recordHeartbeat(RegisteredDevice $device, ?array $hardwareMetadata = null): void
    {
        $updates = ['last_seen_at' => now()];

        if ($hardwareMetadata !== null) {
            $current = $device->hardware_metadata ?? [];
            $updates['hardware_metadata'] = array_merge($current, $hardwareMetadata);
        }

        $device->update($updates);
    }
}
