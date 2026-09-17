<?php

namespace App\Domain\Shared\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class IdempotencyRecord extends Model
{
    use HasUuids;

    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    protected $table = 'idempotency_records';

    protected $fillable = [
        'id',
        'scope',
        'idempotency_key',
        'request_hash',
        'status',
        'resource_type',
        'resource_id',
        'response_code',
        'response_headers',
        'response_body',
        'locked_at',
        'expires_at',
    ];

    protected $casts = [
        'response_headers' => 'array',
        'response_body' => 'array',
        'locked_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isLockExpired(int $timeoutSeconds = 30): bool
    {
        return $this->locked_at->addSeconds($timeoutSeconds)->isPast();
    }

    public function matchesHash(string $hash): bool
    {
        return hash_equals($this->request_hash, $hash);
    }
}
