<?php

namespace App\Http\Controllers\Api;

use App\Domain\Identity\Services\AuthenticationService;
use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffAuthController extends Controller
{
    public function __construct(
        protected AuthenticationService $authService
    ) {}

    /**
     * Authenticate operational staff via PIN for a registered POS branch terminal.
     */
    public function authenticate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'organization_id' => ['required', 'uuid'],
            'branch_id' => ['required', 'uuid'],
            'pin' => ['required', 'string', 'min:4', 'max:8'],
            'staff_code' => ['nullable', 'string'],
        ]);

        try {
            if (!empty($validated['staff_code'])) {
                $staff = $this->authService->authenticateStaff(
                    $validated['organization_id'],
                    $validated['branch_id'],
                    $validated['staff_code'],
                    $validated['pin']
                );
            } else {
                $staff = $this->authService->authenticateStaffByPinOnly(
                    $validated['organization_id'],
                    $validated['branch_id'],
                    $validated['pin']
                );
            }

            return response()->json([
                'status' => 'authenticated',
                'staff' => [
                    'id' => $staff->id,
                    'staff_code' => $staff->staff_code,
                    'display_name' => $staff->display_name,
                    'role_title' => $staff->role_title,
                    'organization_id' => $staff->organization_id,
                    'branch_id' => $validated['branch_id'],
                ],
            ]);
        } catch (AuthenticationException $e) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => $e->getMessage(),
            ], 401);
        }
    }
}
