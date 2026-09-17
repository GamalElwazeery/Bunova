<?php

namespace App\Domain\Identity\Models;

use App\Domain\Identity\Traits\BelongsToOrganization;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;

class StaffIdentity extends Model
{
    use HasUuids;
    use SoftDeletes;
    use BelongsToOrganization;

    protected $table = 'staff_identities';

    protected $fillable = [
        'organization_id',
        'primary_branch_id',
        'user_id',
        'staff_code',
        'display_name',
        'role_title',
        'pin_hash',
        'status',
        'settings',
    ];

    protected $attributes = [
        'role_title' => 'staff',
        'status' => 'active',
    ];

    protected $hidden = [
        'pin_hash',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function primaryBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'primary_branch_id');
    }

    public function assignedBranches(): BelongsToMany
    {
        return $this->belongsToMany(
            Branch::class,
            'staff_branch_assignments',
            'staff_identity_id',
            'branch_id'
        )->withPivot('is_primary')->withTimestamps();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function setPin(string $pin): void
    {
        $this->pin_hash = Hash::make($pin);
    }

    public function verifyPin(string $pin): bool
    {
        return Hash::check($pin, $this->pin_hash);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function roleAssignments(): HasMany
    {
        return $this->hasMany(StaffRoleAssignment::class, 'staff_identity_id');
    }

    public function assignRole(Role|string $role, ?string $branchId = null): StaffRoleAssignment
    {
        $roleId = $role instanceof Role ? $role->id : $role;

        return $this->roleAssignments()->create([
            'organization_id' => $this->organization_id,
            'role_id' => $roleId,
            'branch_id' => $branchId,
        ]);
    }

    public function hasPermission(string $permission, ?string $branchId = null): bool
    {
        // 1. Staff must be active
        if (!$this->isActive()) {
            return false;
        }

        // 2. Platform permissions can NEVER be granted to operational staff
        if (str_starts_with($permission, 'platform.')) {
            return false;
        }

        // 3. Find active role assignments matching scope
        $assignments = $this->roleAssignments()->with('role.permissions')->get();

        foreach ($assignments as $assignment) {
            // Check branch scope:
            // Role applies if assignment is organization-wide (branch_id is null)
            // OR if assignment branch_id matches requested branchId
            $appliesToBranch = $assignment->branch_id === null || ($branchId !== null && $assignment->branch_id === $branchId);

            if ($appliesToBranch && $assignment->role) {
                if ($assignment->role->permissions->contains('id', $permission)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isAssignedToBranch(string $branchId): bool
    {
        if ($this->primary_branch_id === $branchId) {
            return true;
        }

        return $this->assignedBranches()->where('branches.id', $branchId)->exists();
    }
}
