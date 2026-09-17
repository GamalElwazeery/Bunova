<?php

namespace App\Domain\Identity\Models;

use App\Domain\Identity\Traits\BelongsToOrganization;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function isAssignedToBranch(string $branchId): bool
    {
        if ($this->primary_branch_id === $branchId) {
            return true;
        }

        return $this->assignedBranches()->where('branches.id', $branchId)->exists();
    }
}
