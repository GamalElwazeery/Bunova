<?php

namespace App\Domain\Identity\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $table = 'organizations';

    protected $fillable = [
        'slug',
        'name',
        'legal_name',
        'tax_identifier',
        'status',
        'default_currency',
        'default_timezone',
        'default_locale',
        'settings',
    ];

    protected $attributes = [
        'status' => 'active',
        'default_currency' => 'EGP',
        'default_timezone' => 'Africa/Cairo',
        'default_locale' => 'ar',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class, 'organization_id');
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class, 'organization_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }
}
