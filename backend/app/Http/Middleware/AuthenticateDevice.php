<?php

namespace App\Http\Middleware;

use App\Domain\Identity\Services\DeviceRegistryService;
use App\Domain\Identity\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateDevice
{
    public function __construct(
        protected DeviceRegistryService $deviceService
    ) {}

    /**
     * Handle an incoming request from a registered operational device.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Device-Token');

        if (!$token) {
            $authHeader = $request->header('Authorization');
            if ($authHeader && str_starts_with($authHeader, 'Bearer bnd_')) {
                $token = substr($authHeader, 7);
            }
        }

        if (!$token) {
            return response()->json([
                'message' => 'Device authentication token required.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $device = $this->deviceService->authenticate($token);

        if (!$device) {
            return response()->json([
                'message' => 'Invalid or revoked device credential.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Set device on request attributes
        $request->attributes->set('device', $device);

        // Bind device tenant context
        TenantContext::setOrganization($device->organization_id);
        TenantContext::setBranch($device->branch_id);

        return $next($request);
    }
}
