<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HealthCheckController extends Controller
{
    /**
     * Inspect health across database, cache, and storage services.
     */
    public function __invoke(): JsonResponse
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
}
