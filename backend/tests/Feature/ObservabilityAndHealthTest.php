<?php

namespace Tests\Feature;

use App\Domain\Identity\TenantContext;
use App\Http\Middleware\CorrelationIdMiddleware;
use App\Support\Logging\StructuredJsonFormatter;
use App\Support\Observability\ErrorTracker;
use App\Support\Observability\MetricsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Monolog\Level;
use Monolog\LogRecord;
use RuntimeException;
use Tests\TestCase;

class ObservabilityAndHealthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        TenantContext::clear();
    }

    protected function tearDown(): void
    {
        TenantContext::clear();
        parent::tearDown();
    }

    public function test_liveness_endpoint_returns_ok_and_correlation_id(): void
    {
        $response = $this->getJson('/api/v1/health/liveness');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'alive',
            ])
            ->assertHeader(CorrelationIdMiddleware::HEADER_NAME);

        $this->assertNotEmpty($response->json('correlation_id'));
        $this->assertNotEmpty($response->json('timestamp'));
    }

    public function test_readiness_endpoint_returns_healthy_with_all_subsystem_checks(): void
    {
        $response = $this->getJson('/api/v1/health/readiness');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'healthy',
                'environment' => config('app.env'),
            ])
            ->assertJsonStructure([
                'status',
                'timestamp',
                'environment',
                'correlation_id',
                'checks' => [
                    'database' => ['status', 'driver', 'latency_ms'],
                    'cache' => ['status', 'store', 'latency_ms'],
                    'storage' => ['status', 'disk'],
                    'outbox' => ['status', 'pending_events'],
                    'queue' => ['status', 'connection'],
                ],
            ]);

        $this->assertSame('up', $response->json('checks.database.status'));
        $this->assertSame('up', $response->json('checks.cache.status'));
        $this->assertSame('up', $response->json('checks.storage.status'));
        $this->assertSame('up', $response->json('checks.outbox.status'));
        $this->assertSame('up', $response->json('checks.queue.status'));
    }

    public function test_structured_json_formatter_formats_and_redacts_secrets(): void
    {
        $formatter = new StructuredJsonFormatter();

        $logRecord = new LogRecord(
            datetime: new \DateTimeImmutable('2026-09-17T00:00:00Z'),
            channel: 'production',
            level: Level::Info,
            message: 'User authentication processed',
            context: [
                'user_id' => 'usr-123',
                'email' => 'nour@bunova.test',
                'pin' => '1234',
                'password' => 'super_secret_pw',
                'token' => 'bnd_device_token_xyz',
                'correlation_id' => 'trace-corr-999',
            ]
        );

        $outputJson = $formatter->format($logRecord);
        $decoded = json_decode($outputJson, true);

        $this->assertIsArray($decoded);
        $this->assertSame('INFO', $decoded['level']);
        $this->assertSame('User authentication processed', $decoded['message']);
        $this->assertSame('trace-corr-999', $decoded['correlation_id']);

        // Sensitive credentials must be redacted
        $this->assertSame('[REDACTED]', $decoded['context']['pin']);
        $this->assertSame('[REDACTED]', $decoded['context']['password']);
        $this->assertSame('[REDACTED]', $decoded['context']['token']);

        // Non-sensitive context is preserved
        $this->assertSame('usr-123', $decoded['context']['user_id']);
        $this->assertSame('nour@bunova.test', $decoded['context']['email']);
    }

    public function test_error_tracker_captures_exception_with_correlation_and_redaction(): void
    {
        $tracker = app(ErrorTracker::class);

        $exception = new RuntimeException('Connection timed out to payment gateway', 504);

        $errorEventId = $tracker->captureException($exception, [
            'order_id' => 'ord-789',
            'api_key' => 'live_sec_key_12345',
            'correlation_id' => 'err-trace-111',
        ]);

        $this->assertNotEmpty($errorEventId);
        $this->assertTrue(\Illuminate\Support\Str::isUuid($errorEventId));
    }

    public function test_metrics_service_records_counters_gauges_and_timers_with_tags(): void
    {
        $metrics = app(MetricsService::class);

        // Counter
        $metrics->increment(MetricsService::METRIC_API_REQUEST, 1, ['route' => 'orders.store']);
        $metrics->increment(MetricsService::METRIC_API_REQUEST, 2, ['route' => 'orders.store']);

        $count = $metrics->get(MetricsService::METRIC_API_REQUEST, ['route' => 'orders.store']);
        $this->assertSame(3, $count);

        // Gauge
        $metrics->gauge(MetricsService::METRIC_QUEUE_DEPTH, 42.0, ['queue' => 'default']);
        $gauge = $metrics->get(MetricsService::METRIC_QUEUE_DEPTH, ['queue' => 'default']);
        $this->assertSame(42.0, $gauge);

        // Timer
        $metrics->timing(MetricsService::METRIC_API_LATENCY, 120.5, ['endpoint' => '/api/v1/branches']);
        $metrics->timing(MetricsService::METRIC_API_LATENCY, 80.5, ['endpoint' => '/api/v1/branches']);

        $timerData = $metrics->get(MetricsService::METRIC_API_LATENCY, ['endpoint' => '/api/v1/branches']);
        $this->assertIsArray($timerData);
        $this->assertSame(2, $timerData['count']);
        $this->assertSame(100.5, $timerData['avg_ms']);
        $this->assertSame(80.5, $timerData['min_ms']);
        $this->assertSame(120.5, $timerData['max_ms']);
    }
}
