<?php

namespace App\Domain\Shared\Models;

use App\Domain\Identity\Models\Branch;
use App\Domain\Identity\Models\Organization;
use App\Domain\Identity\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutboxEvent extends Model
{
    use HasUuids;
    use BelongsToOrganization;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_DISPATCHED = 'dispatched';
    public const STATUS_FAILED = 'failed';

    protected $table = 'outbox_events';

    protected $fillable = [
        'event_name',
        'aggregate_type',
        'aggregate_id',
        'organization_id',
        'branch_id',
        'correlation_id',
        'actor_id',
        'actor_type',
        'payload',
        'status',
        'attempts',
        'dispatched_at',
        'last_error',
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING,
        'attempts' => 0,
        'actor_type' => 'staff',
    ];

    protected $casts = [
        'payload' => 'array',
        'attempts' => 'integer',
        'dispatched_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isDispatched(): bool
    {
        return $this->status === self::STATUS_DISPATCHED;
    }

    public function markDispatched(): void
    {
        $this->update([
            'status' => self::STATUS_DISPATCHED,
            'dispatched_at' => now(),
            'last_error' => null,
        ]);
    }

    public function markFailed(string $error): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'attempts' => $this->attempts + 1,
            'last_error' => $error,
        ]);
    }
}
