<?php

namespace App\Domain\Shared\Models;

use App\Domain\Identity\Models\Branch;
use App\Domain\Identity\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class AuditEvent extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $table = 'audit_events';

    // Mandatory Action Families per AUDIT_EVENT_CONTRACT.md
    public const ACTION_AUTH_SECURITY = 'security.auth';
    public const ACTION_ROLE_PERMISSION_CHANGED = 'security.role_permission_changed';
    public const ACTION_CAPABILITY_CONFIG_CHANGED = 'config.capability_changed';
    public const ACTION_PRICE_OVERRIDE = 'finance.price_override';
    public const ACTION_DISCOUNT_APPLIED = 'finance.discount_applied';
    public const ACTION_ITEM_VOIDED = 'order.item_voided';
    public const ACTION_PAYMENT_REFUNDED = 'finance.refund_issued';
    public const ACTION_COMPLIMENTARY_VALUE = 'finance.complimentary_value';
    public const ACTION_CASH_DRAWER_OPENED = 'cash.drawer_opened';
    public const ACTION_CASH_MOVEMENT = 'cash.movement';
    public const ACTION_SHIFT_VARIANCE_APPROVED = 'cash.shift_variance_approved';
    public const ACTION_STOCK_ADJUSTMENT = 'inventory.stock_adjustment';
    public const ACTION_TIMED_DURATION_OVERRIDE = 'timed_service.duration_override';
    public const ACTION_WIFI_CONFIG_CHANGED = 'wifi.credential_config_changed';
    public const ACTION_FISCAL_CORRECTION = 'fiscal.correction';
    public const ACTION_DEVICE_REGISTERED = 'device.registered';
    public const ACTION_DEVICE_REVOKED = 'device.revoked';
    public const ACTION_SENSITIVE_EXPORT = 'compliance.sensitive_export';

    protected $fillable = [
        'id',
        'organization_id',
        'branch_id',
        'occurred_at',
        'actor_id',
        'actor_type',
        'device_id',
        'action_key',
        'target_type',
        'target_id',
        'correlation_id',
        'reason',
        'payload',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Strict append-only guard: prevent updates.
     */
    public function update(array $attributes = [], array $options = []): bool
    {
        throw new LogicException('Audit events are strictly append-only and cannot be mutated.');
    }

    /**
     * Strict append-only guard: prevent deletions.
     */
    public function delete(): ?bool
    {
        throw new LogicException('Audit events are strictly append-only and cannot be deleted.');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function scopeForOrganization(Builder $query, string $organizationId): Builder
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeForBranch(Builder $query, string $branchId): Builder
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeForTarget(Builder $query, string $targetType, string $targetId): Builder
    {
        return $query->where('target_type', $targetType)
            ->where('target_id', $targetId);
    }

    public function scopeForActor(Builder $query, string $actorId): Builder
    {
        return $query->where('actor_id', $actorId);
    }

    public function scopeForAction(Builder $query, string $actionKey): Builder
    {
        return $query->where('action_key', $actionKey);
    }

    public function scopeRecentFirst(Builder $query): Builder
    {
        return $query->orderBy('occurred_at', 'desc');
    }
}
