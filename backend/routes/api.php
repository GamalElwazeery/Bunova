<?php

use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\HealthCheckController;
use App\Http\Controllers\Api\StaffAuthController;
use App\Http\Middleware\AuthenticateDevice;
use Illuminate\Support\Facades\Route;

// Top-level health endpoints
Route::get('/health', HealthCheckController::class)->name('api.health');
Route::get('/health/liveness', [HealthCheckController::class, 'liveness'])->name('api.health.liveness');
Route::get('/health/readiness', [HealthCheckController::class, 'readiness'])->name('api.health.readiness');

// Versioned API v1 routes
Route::prefix('v1')->group(function () {
    // Health & Observability probes
    Route::get('/health', HealthCheckController::class)->name('api.v1.health');
    Route::get('/health/liveness', [HealthCheckController::class, 'liveness'])->name('api.v1.health.liveness');
    Route::get('/health/readiness', [HealthCheckController::class, 'readiness'])->name('api.v1.health.readiness');

    Route::post('/pos/staff/authenticate', [StaffAuthController::class, 'authenticate'])->name('api.v1.pos.staff.authenticate');

    // Device registration, revocation & management
    Route::post('/devices/enroll', [DeviceController::class, 'enroll'])->name('api.v1.devices.enroll');
    Route::post('/devices/{id}/revoke', [DeviceController::class, 'revoke'])->name('api.v1.devices.revoke');
    Route::post('/devices/{id}/rotate-credentials', [DeviceController::class, 'rotateCredentials'])->name('api.v1.devices.rotate_credentials');

    // Authenticated device endpoints
    Route::middleware(AuthenticateDevice::class)->group(function () {
        Route::get('/devices/me', [DeviceController::class, 'me'])->name('api.v1.devices.me');
        Route::post('/devices/heartbeat', [DeviceController::class, 'heartbeat'])->name('api.v1.devices.heartbeat');
    });

    // Branch management (versioned API, filtering, pagination, idempotency)
    Route::get('/branches', [BranchController::class, 'index'])->name('api.v1.branches.index');
    Route::post('/branches', [BranchController::class, 'store'])->middleware('idempotent')->name('api.v1.branches.store');
    Route::get('/branches/{id}', [BranchController::class, 'show'])->name('api.v1.branches.show');
});
