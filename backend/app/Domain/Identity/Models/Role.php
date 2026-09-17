<?php

namespace App\Domain\Identity\Models;

use App\Domain\Identity\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasUuids;
    use BelongsToOrganization;

    protected $table = 'roles';

    protected $fillable = [
        'organization_id',
        'name',
        'display_name',
        'scope_type',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permissions',
            'role_id',
            'permission_id'
        );
    }

    public function staffAssignments(): HasMany
    {
        return $this->hasMany(StaffRoleAssignment::class, 'role_id');
    }

    public function hasPermission(string $permissionId): bool
    {
        return $this->permissions()->where('permissions.id', $permissionId)->exists();
    }

    public function givePermission(Permission|string $permission): void
    {
        $id = $permission instanceof Permission ? $permission->id : $permission;
        if (!$this->permissions()->where('permissions.id', $id)->exists()) {
            $this->permissions()->attach($id);
        }
    }
}
