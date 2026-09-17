<?php

namespace App\Http\Controllers\Api;

use App\Domain\Identity\Models\RegisteredDevice;
use App\Domain\Identity\Services\DeviceRegistryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceController extends Controller
{
    public function __construct(
        protected DeviceRegistryService $deviceService
    ) {}

    /**
     * Enroll a new physical operational device.
     */
    public function enroll(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'organization_id' => ['required', 'uuid'],
            'branch_id' => ['required', 'uuid'],
            'device_code' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'device_type' => ['nullable', 'string', 'in:pos_register,handheld_waiter,kds_kitchen,kds_bar,customer_facing_display,manager_tablet'],
            'capabilities' => ['nullable', 'array'],
            'capabilities.*' => ['string'],
            'hardware_metadata' => ['nullable', 'array'],
            'settings' => ['nullable', 'array'],
        ]);

        try {
            $result = $this->deviceService->enroll($validated);

            return response()->json([
                'message' => 'Device enrolled successfully.',
                'device' => $result['device'],
                'device_token' => $result['token'],
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Get identity and operational profile of authenticated device.
     */
    public function me(Request $request): JsonResponse
    {
        /** @var RegisteredDevice $device */
        $device = $request->attributes->get('device');

        return response()->json([
            'device' => $device,
        ]);
    }

    /**
     * Record device heartbeat and telemetry.
     */
    public function heartbeat(Request $request): JsonResponse
    {
        /** @var RegisteredDevice $device */
        $device = $request->attributes->get('device');

        $this->deviceService->recordHeartbeat(
            $device,
            $request->input('hardware_metadata')
        );

        return response()->json([
            'status' => 'ok',
            'last_seen_at' => $device->fresh()->last_seen_at->toIso8601String(),
        ]);
    }

    /**
     * Revoke a registered device.
     */
    public function revoke(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $device = RegisteredDevice::find($id);

        if (!$device) {
            return response()->json([
                'message' => 'Device not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $this->deviceService->revoke($device, $validated['reason']);

        return response()->json([
            'message' => 'Device revoked successfully.',
            'status' => RegisteredDevice::STATUS_REVOKED,
        ]);
    }

    /**
     * Rotate credentials for a device.
     */
    public function rotateCredentials(string $id): JsonResponse
    {
        $device = RegisteredDevice::find($id);

        if (!$device) {
            return response()->json([
                'message' => 'Device not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        try {
            $newToken = $this->deviceService->rotateCredentials($device);

            return response()->json([
                'message' => 'Credentials rotated successfully.',
                'device_token' => $newToken,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
