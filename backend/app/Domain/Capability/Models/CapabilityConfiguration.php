<?php

namespace App\Domain\Capability\Models;

use App\Domain\Identity\Models\Branch;
use App\Domain\Identity\Models\Organization;
use App\Domain\Identity\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapabilityConfiguration extends Model
{
    use HasUuids;
    use SoftDeletes;
    use BelongsToOrganization;

    public const STATUS_ENABLED = 'enabled';
    public const STATUS_NEEDS_CONFIGURATION = 'needs_configuration';
    public const STATUS_DISABLED = 'disabled';

    protected $table = 'capability_configurations';

    protected $fillable = [
        'organization_id',
        'branch_id',
        'capability_key',
        'status',
        'config',
        'version',
        'enabled_at',
        'disabled_at',
        'disable_reason',
    ];

    protected $attributes = [
        'status' => self::STATUS_ENABLED,
        'version' => '1.0.0',
    ];

    protected $casts = [
        'config' => 'array',
        'enabled_at' => 'datetime',
        'disabled_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function isEnabled(): bool
    {
        return $this->status === self::STATUS_ENABLED;
    }

    public function needsConfiguration(): bool
    {
        return $this->status === self::STATUS_NEEDS_CONFIGURATION;
    }

    public function isDisabled(): bool
    {
        return $this->status === self::STATUS_DISABLED;
    }

    public function isBranchOverride(): bool
    {
        return $this->branch_id !== null;
    }
}
