<?php

namespace App\Support\Observability;

use App\Domain\Identity\TenantContext;
use Illuminate\Support\Facades\Cache;

class MetricsService
{
    // Canonical operational metric keys per OBSERVABILITY_AND_OPERATIONS.md
    public const METRIC_API_REQUEST = 'api.request.count';
    public const METRIC_API_LATENCY = 'api.request.latency_ms';
    public const METRIC_API_ERROR = 'api.request.error';
    public const METRIC_QUEUE_DEPTH = 'queue.depth';
    public const METRIC_OUTBOX_PENDING = 'outbox.pending.count';
    public const METRIC_INBOX_PROCESSED = 'inbox.processed.count';
    public const METRIC_DEVICE_HEARTBEAT = 'device.heartbeat.count';
    public const METRIC_SYNC_CONFLICT = 'sync.conflict.count';
    public const METRIC_PAYMENT_UNKNOWN = 'payment.unknown_state.count';
    public const METRIC_FISCAL_PENDING = 'fiscal.pending.count';

    protected array $inMemoryMetrics = [];

    /**
     * Increment a counter metric.
     */
    public function increment(string $metric, int $amount = 1, array $tags = []): void
    {
        $key = $this->buildMetricKey($metric, $tags);

        if (!isset($this->inMemoryMetrics[$key])) {
            $this->inMemoryMetrics[$key] = [
                'type' => 'counter',
                'metric' => $metric,
                'tags' => $tags,
                'value' => 0,
            ];
        }

        $this->inMemoryMetrics[$key]['value'] += $amount;
    }

    /**
     * Set a gauge metric value.
     */
    public function gauge(string $metric, float $value, array $tags = []): void
    {
        $key = $this->buildMetricKey($metric, $tags);

        $this->inMemoryMetrics[$key] = [
            'type' => 'gauge',
            'metric' => $metric,
            'tags' => $tags,
            'value' => $value,
        ];
    }

    /**
     * Record a duration/timing metric in milliseconds.
     */
    public function timing(string $metric, float $milliseconds, array $tags = []): void
    {
        $key = $this->buildMetricKey($metric, $tags);

        if (!isset($this->inMemoryMetrics[$key])) {
            $this->inMemoryMetrics[$key] = [
                'type' => 'timer',
                'metric' => $metric,
                'tags' => $tags,
                'count' => 0,
                'total_ms' => 0.0,
                'min_ms' => $milliseconds,
                'max_ms' => $milliseconds,
                'avg_ms' => $milliseconds,
            ];
        }

        $entry = &$this->inMemoryMetrics[$key];
        $entry['count']++;
        $entry['total_ms'] += $milliseconds;
        $entry['min_ms'] = min($entry['min_ms'], $milliseconds);
        $entry['max_ms'] = max($entry['max_ms'], $milliseconds);
        $entry['avg_ms'] = round($entry['total_ms'] / $entry['count'], 2);
    }

    /**
     * Retrieve metric record by key and tags.
     */
    public function get(string $metric, array $tags = []): mixed
    {
        $key = $this->buildMetricKey($metric, $tags);

        return $this->inMemoryMetrics[$key]['value'] ?? $this->inMemoryMetrics[$key] ?? null;
    }

    /**
     * Get all currently recorded metrics.
     */
    public function all(): array
    {
        return array_values($this->inMemoryMetrics);
    }

    /**
     * Build deterministic tag-bound metric key.
     */
    protected function buildMetricKey(string $metric, array $tags): string
    {
        // Add active tenant tags if available
        if (!isset($tags['organization_id']) && TenantContext::hasOrganization()) {
            $tags['organization_id'] = TenantContext::getOrganizationId();
        }

        ksort($tags);
        $tagStr = '';
        foreach ($tags as $k => $v) {
            $tagStr .= ",{$k}={$v}";
        }

        return $metric . ($tagStr !== '' ? '{' . ltrim($tagStr, ',') . '}' : '');
    }
}
