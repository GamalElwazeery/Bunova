<?php

namespace App\Domain\Identity\Models;

use App\Domain\Identity\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffRoleAssignment extends Model
{
    use HasUuids;
    use BelongsToOrganization;

    protected $table = 'staff_role_assignments';

    protected $fillable = [
        'organization_id',
        'staff_identity_id',
        'role_id',
        'branch_id',
    ];

    public function staffIdentity(): BelongsTo
    {
        return $this->belongsTo(StaffIdentity::class, 'staff_identity_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
