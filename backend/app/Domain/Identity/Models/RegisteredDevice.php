<?php

namespace App\Domain\Identity\Models;

use App\Domain\Identity\Traits\BelongsToBranch;
use App\Domain\Identity\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegisteredDevice extends Model
{
    use HasUuids;
    use SoftDeletes;
    use BelongsToOrganization;
    use BelongsToBranch;

    public const TYPE_POS_REGISTER = 'pos_register';
    public const TYPE_HANDHELD_WAITER = 'handheld_waiter';
    public const TYPE_KDS_KITCHEN = 'kds_kitchen';
    public const TYPE_KDS_BAR = 'kds_bar';
    public const TYPE_CUSTOMER_FACING_DISPLAY = 'customer_facing_display';
    public const TYPE_MANAGER_TABLET = 'manager_tablet';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_REVOKED = 'revoked';

    public const CAPABILITY_CASH_DRAWER = 'cash_drawer';
    public const CAPABILITY_RECEIPT_PRINTING = 'receipt_printing';
    public const CAPABILITY_BARCODE_SCANNER = 'barcode_scanner';
    public const CAPABILITY_CARD_TERMINAL = 'card_terminal';
    public const CAPABILITY_OFFLINE_ORDERS = 'offline_orders';
    public const CAPABILITY_KDS_BUMP_BAR = 'kds_bump_bar';

    protected $table = 'registered_devices';

    protected $fillable = [
        'organization_id',
        'branch_id',
        'device_code',
        'name',
        'device_type',
        'status',
        'api_key_prefix',
        'token_hash',
        'capabilities',
        'hardware_metadata',
        'settings',
        'registered_at',
        'last_seen_at',
        'last_sync_at',
        'revoked_at',
        'revocation_reason',
    ];

    protected $attributes = [
        'status' => self::STATUS_ACTIVE,
        'device_type' => self::TYPE_POS_REGISTER,
    ];

    protected $hidden = [
        'token_hash',
    ];

    protected $casts = [
        'capabilities' => 'array',
        'hardware_metadata' => 'array',
        'settings' => 'array',
        'registered_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'last_sync_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isRevoked(): bool
    {
        return $this->status === self::STATUS_REVOKED;
    }

    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    public function hasCapability(string $capability): bool
    {
        if (!is_array($this->capabilities)) {
            return false;
        }

        return in_array($capability, $this->capabilities, true);
    }

    public function supportsOffline(): bool
    {
        return $this->hasCapability(self::CAPABILITY_OFFLINE_ORDERS);
    }
}
