<?php

use App\Http\Controllers\Api\HealthCheckController;
use App\Http\Controllers\Api\StaffAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthCheckController::class)->name('api.health');
Route::prefix('v1')->group(function () {
    Route::get('/health', HealthCheckController::class)->name('api.v1.health');
    Route::post('/pos/staff/authenticate', [StaffAuthController::class, 'authenticate'])->name('api.v1.pos.staff.authenticate');
});
