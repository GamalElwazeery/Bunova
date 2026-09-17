<?php

namespace App\Domain\Shared\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class InboxMessage extends Model
{
    use HasUuids;

    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PROCESSED = 'processed';
    public const STATUS_FAILED = 'failed';

    protected $table = 'inbox_messages';

    protected $fillable = [
        'id',
        'consumer_name',
        'message_id',
        'event_name',
        'status',
        'payload_hash',
        'attempts',
        'error_message',
        'processed_at',
    ];

    protected $casts = [
        'attempts' => 'integer',
        'processed_at' => 'datetime',
    ];

    public function isProcessed(): bool
    {
        return $this->status === self::STATUS_PROCESSED;
    }

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function markProcessed(): void
    {
        $this->update([
            'status' => self::STATUS_PROCESSED,
            'processed_at' => Carbon::now(),
            'error_message' => null,
        ]);
    }

    public function markFailed(string $error): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $error,
        ]);
    }
}
