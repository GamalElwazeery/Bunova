<?php

namespace App\Domain\Identity\Models;

use App\Domain\Identity\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasUuids;
    use SoftDeletes;
    use BelongsToOrganization;

    protected $table = 'branches';

    protected $fillable = [
        'organization_id',
        'brand_id',
        'code',
        'name',
        'timezone',
        'status',
        'phone',
        'address_line_1',
        'city',
        'country',
        'tax_registration_number',
        'business_hours',
        'settings',
    ];

    protected $attributes = [
        'status' => 'active',
        'timezone' => 'Africa/Cairo',
        'country' => 'EG',
    ];

    protected $casts = [
        'business_hours' => 'array',
        'settings' => 'array',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getTimezone(): string
    {
        return $this->timezone ?: ($this->organization?->default_timezone ?: 'Africa/Cairo');
    }
}
