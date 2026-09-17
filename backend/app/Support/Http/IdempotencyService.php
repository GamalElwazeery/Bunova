<?php

namespace App\Support\Http;

use App\Domain\Identity\TenantContext;
use App\Domain\Shared\Models\IdempotencyRecord;
use App\Support\Http\Exceptions\IdempotencyConcurrentConflictException;
use App\Support\Http\Exceptions\IdempotencyPayloadMismatchException;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IdempotencyService
{
    public const DEFAULT_TTL_HOURS = 24;
    public const DEFAULT_LOCK_TIMEOUT_SECONDS = 30;

    /**
     * Compute a deterministic hash of the request method, path, and normalized payload.
     */
    public function computeRequestHash(Request $request): string
    {
        $payload = $request->all();
        $normalizedPayload = $this->normalizeArray($payload);

        $dataToHash = [
            'method' => strtoupper($request->method()),
            'path' => $request->path(),
            'payload' => $normalizedPayload,
        ];

        $json = json_encode($dataToHash, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return hash('sha256', (string) $json);
    }

    /**
     * Resolve the isolation scope for this request.
     */
    public function resolveScope(Request $request): string
    {
        $parts = [];

        $orgId = TenantContext::getOrganization() ?? $request->input('organization_id') ?? $request->header('X-Organization-ID');
        if ($orgId) {
            $parts[] = 'org:'.$orgId;
        }

        $branchId = TenantContext::getBranch() ?? $request->input('branch_id') ?? $request->header('X-Branch-ID');
        if ($branchId) {
            $parts[] = 'branch:'.$branchId;
        }

        $deviceId = $request->attributes->get('device')?->id ?? $request->header('X-Device-ID');
        if ($deviceId) {
            $parts[] = 'device:'.$deviceId;
        }

        $userId = $request->user()?->id ?? $request->attributes->get('staff')?->id;
        if ($userId) {
            $parts[] = 'actor:'.$userId;
        }

        $parts[] = 'route:'.$request->path();

        $scope = implode('|', $parts);

        // Limit string length to 255 chars
        return strlen($scope) > 255 ? substr($scope, 0, 255) : $scope;
    }

    /**
     * Claim lock or return existing idempotent record.
     *
     * @throws IdempotencyPayloadMismatchException
     * @throws IdempotencyConcurrentConflictException
     */
    public function claimLock(
        string $scope,
        string $key,
        string $requestHash,
        int $ttlHours = self::DEFAULT_TTL_HOURS,
        int $lockTimeoutSeconds = self::DEFAULT_LOCK_TIMEOUT_SECONDS
    ): IdempotencyRecord {
        try {
            return DB::transaction(function () use ($scope, $key, $requestHash, $ttlHours, $lockTimeoutSeconds) {
                /** @var IdempotencyRecord|null $record */
                $record = IdempotencyRecord::where('scope', $scope)
                    ->where('idempotency_key', $key)
                    ->lockForUpdate()
                    ->first();

                if ($record) {
                    // Check payload mismatch
                    if (!$record->matchesHash($requestHash)) {
                        throw new IdempotencyPayloadMismatchException(
                            'Idempotency key was previously used with a materially different payload.'
                        );
                    }

                    // If completed, return for replay
                    if ($record->isCompleted()) {
                        return $record;
                    }

                    // If currently in progress, check lock expiration
                    if ($record->isInProgress()) {
                        if (!$record->isLockExpired($lockTimeoutSeconds)) {
                            throw new IdempotencyConcurrentConflictException(
                                'A request with this idempotency key is currently in progress. Please retry shortly.'
                            );
                        }

                        // Lock has timed out; re-claim
                        $record->update([
                            'locked_at' => Carbon::now(),
                            'expires_at' => Carbon::now()->addHours($ttlHours),
                        ]);

                        return $record;
                    }

                    // If failed previously, allow retry
                    $record->update([
                        'status' => IdempotencyRecord::STATUS_IN_PROGRESS,
                        'locked_at' => Carbon::now(),
                        'expires_at' => Carbon::now()->addHours($ttlHours),
                    ]);

                    return $record;
                }

                return IdempotencyRecord::create([
                    'id' => (string) Str::uuid7(),
                    'scope' => $scope,
                    'idempotency_key' => $key,
                    'request_hash' => $requestHash,
                    'status' => IdempotencyRecord::STATUS_IN_PROGRESS,
                    'locked_at' => Carbon::now(),
                    'expires_at' => Carbon::now()->addHours($ttlHours),
                ]);
            });
        } catch (QueryException $e) {
            // In case of race condition on insert, re-query the created record
            $existing = IdempotencyRecord::where('scope', $scope)
                ->where('idempotency_key', $key)
                ->first();

            if ($existing) {
                if (!$existing->matchesHash($requestHash)) {
                    throw new IdempotencyPayloadMismatchException(
                        'Idempotency key was previously used with a materially different payload.'
                    );
                }

                if ($existing->isCompleted()) {
                    return $existing;
                }

                throw new IdempotencyConcurrentConflictException(
                    'A request with this idempotency key is currently in progress. Please retry shortly.'
                );
            }

            throw $e;
        }
    }

    /**
     * Mark an idempotency record as completed with cached response.
     */
    public function markCompleted(
        IdempotencyRecord $record,
        int $statusCode,
        array $headers,
        mixed $body,
        ?string $resourceType = null,
        ?string $resourceId = null
    ): void {
        $allowedHeaders = ['content-type', 'x-correlation-id', 'x-bunova-version'];
        $cleanHeaders = [];
        foreach ($headers as $k => $v) {
            $lower = strtolower($k);
            if (in_array($lower, $allowedHeaders, true)) {
                $cleanHeaders[$lower] = is_array($v) ? $v[0] : $v;
            }
        }

        $record->update([
            'status' => IdempotencyRecord::STATUS_COMPLETED,
            'response_code' => $statusCode,
            'response_headers' => $cleanHeaders,
            'response_body' => $body,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
        ]);
    }

    /**
     * Mark an idempotency record as failed so client can retry.
     */
    public function markFailed(IdempotencyRecord $record): void
    {
        $record->update([
            'status' => IdempotencyRecord::STATUS_FAILED,
        ]);
    }

    /**
     * Recursively sort array keys to guarantee deterministic serialization.
     */
    protected function normalizeArray(mixed $data): mixed
    {
        if (!is_array($data)) {
            return $data;
        }

        $normalized = [];
        $keys = array_keys($data);
        sort($keys);

        foreach ($keys as $key) {
            $normalized[$key] = $this->normalizeArray($data[$key]);
        }

        return $normalized;
    }
}
