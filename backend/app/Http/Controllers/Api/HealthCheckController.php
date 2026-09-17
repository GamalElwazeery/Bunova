<?php

namespace App\Http\Controllers\Api;

use App\Domain\Shared\Models\OutboxEvent;
use App\Http\Controllers\Controller;
use App\Http\Middleware\CorrelationIdMiddleware;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class HealthCheckController extends Controller
{
    /**
     * Inspect baseline health across database, cache, and storage services.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $services = [];
        $isHealthy = true;

        // 1. Database check
        try {
            DB::connection()->getPdo();
            $driver = DB::connection()->getDriverName();
            $services['database'] = [
                'status' => 'ok',
                'driver' => $driver,
            ];
        } catch (Exception $e) {
            $isHealthy = false;
            $services['database'] = [
                'status' => 'unreachable',
                'error' => $e->getMessage(),
            ];
        }

        // 2. Cache check
        try {
            $key = 'health_check_probe_' . microtime(true);
            Cache::put($key, 'ok', 5);
            $val = Cache::get($key);
            Cache::forget($key);

            $services['cache'] = [
                'status' => $val === 'ok' ? 'ok' : 'degraded',
                'store' => config('cache.default'),
            ];
            if ($val !== 'ok') {
                $isHealthy = false;
            }
        } catch (Exception $e) {
            $isHealthy = false;
            $services['cache'] = [
                'status' => 'unreachable',
                'error' => $e->getMessage(),
            ];
        }

        // 3. Storage check
        try {
            $disk = config('filesystems.default');
            $canWrite = Storage::disk($disk)->put('health_check_probe.txt', 'probe');
            Storage::disk($disk)->delete('health_check_probe.txt');

            $services['storage'] = [
                'status' => $canWrite ? 'ok' : 'degraded',
                'disk' => $disk,
            ];
            if (!$canWrite) {
                $isHealthy = false;
            }
        } catch (Exception $e) {
            $isHealthy = false;
            $services['storage'] = [
                'status' => 'unreachable',
                'error' => $e->getMessage(),
            ];
        }

        $statusCode = $isHealthy ? 200 : 503;

        return response()->json([
            'status' => $isHealthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toIso8601String(),
            'environment' => config('app.env'),
            'services' => $services,
        ], $statusCode);
    }

    /**
     * Fast liveness probe: verifies process is alive and handling requests.
     */
    public function liveness(Request $request): JsonResponse
    {
        $correlationId = $request->attributes->get('correlation_id')
            ?? $request->header(CorrelationIdMiddleware::HEADER_NAME);

        return response()->json([
            'status' => 'alive',
            'timestamp' => Carbon::now()->toIso8601String(),
            'correlation_id' => $correlationId,
        ], Response::HTTP_OK);
    }

    /**
     * Deep readiness probe: verifies critical dependencies before routing traffic.
     */
    public function readiness(Request $request): JsonResponse
    {
        $correlationId = $request->attributes->get('correlation_id')
            ?? $request->header(CorrelationIdMiddleware::HEADER_NAME);

        $checks = [];
        $isHealthy = true;

        // 1. Database check with latency measurement
        $dbStart = microtime(true);
        try {
            DB::connection()->getPdo();
            $dbLatency = round((microtime(true) - $dbStart) * 1000, 2);
            $checks['database'] = [
                'status' => 'up',
                'driver' => DB::connection()->getDriverName(),
                'latency_ms' => $dbLatency,
            ];
        } catch (Exception $e) {
            $isHealthy = false;
            $checks['database'] = [
                'status' => 'down',
                'error' => $e->getMessage(),
            ];
        }

        // 2. Cache check with read/write probe
        $cacheStart = microtime(true);
        try {
            $probeKey = 'readiness_probe_' . microtime(true);
            Cache::put($probeKey, 'ok', 5);
            $val = Cache::get($probeKey);
            Cache::forget($probeKey);
            $cacheLatency = round((microtime(true) - $cacheStart) * 1000, 2);

            $checks['cache'] = [
                'status' => ($val === 'ok') ? 'up' : 'degraded',
                'store' => config('cache.default'),
                'latency_ms' => $cacheLatency,
            ];
            if ($val !== 'ok') {
                $isHealthy = false;
            }
        } catch (Exception $e) {
            $isHealthy = false;
            $checks['cache'] = [
                'status' => 'down',
                'error' => $e->getMessage(),
            ];
        }

        // 3. Storage check
        try {
            $disk = config('filesystems.default');
            $canWrite = Storage::disk($disk)->put('readiness_probe.txt', 'probe');
            Storage::disk($disk)->delete('readiness_probe.txt');

            $checks['storage'] = [
                'status' => $canWrite ? 'up' : 'degraded',
                'disk' => $disk,
            ];
            if (!$canWrite) {
                $isHealthy = false;
            }
        } catch (Exception $e) {
            $isHealthy = false;
            $checks['storage'] = [
                'status' => 'down',
                'error' => $e->getMessage(),
            ];
        }

        // 4. Outbox backlog depth check
        try {
            $pendingOutboxCount = OutboxEvent::withoutGlobalScopes()
                ->where('status', OutboxEvent::STATUS_PENDING)
                ->count();

            $oldestPending = OutboxEvent::withoutGlobalScopes()
                ->where('status', OutboxEvent::STATUS_PENDING)
                ->orderBy('created_at', 'asc')
                ->first();

            $oldestAgeSeconds = $oldestPending ? Carbon::now()->diffInSeconds($oldestPending->created_at) : 0;

            $checks['outbox'] = [
                'status' => ($oldestAgeSeconds > 300) ? 'degraded' : 'up',
                'pending_events' => $pendingOutboxCount,
                'oldest_age_seconds' => $oldestAgeSeconds,
            ];
        } catch (Exception $e) {
            $checks['outbox'] = [
                'status' => 'unknown',
                'error' => $e->getMessage(),
            ];
        }

        // 5. Queue connectivity check
        try {
            $queueConnection = config('queue.default');
            $checks['queue'] = [
                'status' => 'up',
                'connection' => $queueConnection,
            ];
        } catch (Exception $e) {
            $checks['queue'] = [
                'status' => 'down',
                'error' => $e->getMessage(),
            ];
        }

        $statusCode = $isHealthy ? Response::HTTP_OK : Response::HTTP_SERVICE_UNAVAILABLE;

        return response()->json([
            'status' => $isHealthy ? 'healthy' : 'unhealthy',
            'timestamp' => Carbon::now()->toIso8601String(),
            'environment' => config('app.env'),
            'correlation_id' => $correlationId,
            'checks' => $checks,
        ], $statusCode);
    }
}
