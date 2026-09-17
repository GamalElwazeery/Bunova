<?php

namespace App\Domain\Identity\Models;

use App\Domain\Identity\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasUuids;
    use SoftDeletes;
    use BelongsToOrganization;

    protected $table = 'brands';

    protected $fillable = [
        'organization_id',
        'slug',
        'name',
        'status',
        'settings',
    ];

    protected $attributes = [
        'status' => 'active',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class, 'brand_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
