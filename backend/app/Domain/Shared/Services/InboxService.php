<?php

namespace App\Domain\Shared\Services;

use App\Domain\Shared\Models\InboxMessage;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class InboxService
{
    public const IN_FLIGHT_LOCK_TIMEOUT_SECONDS = 60;

    /**
     * Check if a message has already been processed by a given consumer.
     */
    public function hasProcessed(string $consumerName, string $messageId): bool
    {
        return InboxMessage::where('consumer_name', $consumerName)
            ->where('message_id', $messageId)
            ->where('status', InboxMessage::STATUS_PROCESSED)
            ->exists();
    }

    /**
     * Execute an event consumer idempotently within a database transaction.
     *
     * Guarantees at-least-once deliveries will never double-apply side effects.
     */
    public function executeTransactionally(
        string $consumerName,
        string $messageId,
        string $eventName,
        array $payload,
        callable $handler
    ): mixed {
        $payloadHash = $this->hashPayload($payload);

        /** @var InboxMessage|null $record */
        $record = InboxMessage::where('consumer_name', $consumerName)
            ->where('message_id', $messageId)
            ->first();

        if ($record !== null) {
            if ($record->isProcessed()) {
                Log::info("Idempotent inbox skip: message {$messageId} already processed by {$consumerName}.", [
                    'consumer_name' => $consumerName,
                    'message_id' => $messageId,
                    'event_name' => $eventName,
                ]);

                return null;
            }

            if ($record->isProcessing()) {
                $isFresh = $record->updated_at && $record->updated_at->gt(Carbon::now()->subSeconds(self::IN_FLIGHT_LOCK_TIMEOUT_SECONDS));
                if ($isFresh) {
                    Log::warning("Idempotent inbox concurrent lock: message {$messageId} currently processing by {$consumerName}.", [
                        'consumer_name' => $consumerName,
                        'message_id' => $messageId,
                        'event_name' => $eventName,
                    ]);

                    return null;
                }

                // Lock expired; allow retry
                $record->update([
                    'attempts' => $record->attempts + 1,
                    'updated_at' => Carbon::now(),
                ]);
            } elseif ($record->isFailed()) {
                // Previously failed; allow retry
                $record->update([
                    'status' => InboxMessage::STATUS_PROCESSING,
                    'attempts' => $record->attempts + 1,
                    'updated_at' => Carbon::now(),
                ]);
            }
        } else {
            try {
                $record = InboxMessage::create([
                    'id' => (string) Str::uuid7(),
                    'consumer_name' => $consumerName,
                    'message_id' => $messageId,
                    'event_name' => $eventName,
                    'status' => InboxMessage::STATUS_PROCESSING,
                    'payload_hash' => $payloadHash,
                    'attempts' => 1,
                ]);
            } catch (QueryException $e) {
                // Race condition on insert: re-query
                $existing = InboxMessage::where('consumer_name', $consumerName)
                    ->where('message_id', $messageId)
                    ->first();

                if ($existing && $existing->isProcessed()) {
                    return null;
                }

                throw $e;
            }
        }

        // Execute handler transactionally
        try {
            return DB::transaction(function () use ($handler, $payload, $record) {
                $result = $handler($payload);
                $record->markProcessed();

                return $result;
            });
        } catch (Throwable $e) {
            $record->markFailed($e->getMessage());
            throw $e;
        }
    }

    /**
     * Prune processed inbox records older than retention window.
     */
    public function prune(int $retentionDays = 30): int
    {
        return InboxMessage::where('status', InboxMessage::STATUS_PROCESSED)
            ->where('processed_at', '<', Carbon::now()->subDays($retentionDays))
            ->delete();
    }

    /**
     * Hash array payload deterministically.
     */
    protected function hashPayload(array $payload): string
    {
        ksort($payload);

        return hash('sha256', (string) json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
