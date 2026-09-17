<?php

use App\Http\Controllers\Api\HealthCheckController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthCheckController::class)->name('api.health');
Route::prefix('v1')->group(function () {
    Route::get('/health', HealthCheckController::class)->name('api.v1.health');
});
