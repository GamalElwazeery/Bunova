<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Domain\Identity\Models\Organization;
use App\Domain\Identity\Models\StaffIdentity;
use App\Domain\Identity\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use BelongsToOrganization;

    public const TYPE_PLATFORM_OPERATOR = 'platform_operator';
    public const TYPE_TENANT_USER = 'tenant_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'user_type',
        'status',
        'phone',
        'password',
        'settings',
    ];

    /**
     * Default model attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'user_type' => self::TYPE_TENANT_USER,
        'status' => 'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'settings' => 'array',
        ];
    }

    public function staffIdentity(): HasOne
    {
        return $this->hasOne(StaffIdentity::class, 'user_id');
    }

    public function isPlatformOperator(): bool
    {
        return $this->user_type === self::TYPE_PLATFORM_OPERATOR;
    }

    public function isTenantUser(): bool
    {
        return $this->user_type === self::TYPE_TENANT_USER;
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
