<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\LeadsController;
use App\Http\Controllers\API\NotificationController;

Route::prefix('v1')->group(function() {
    Route::post('auth/login', [AuthController::class, 'login']);
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function() {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('home', [HomeController::class, 'index']);
        Route::get('leads', [LeadsController::class, 'index']);
        Route::get('leads/filters', [LeadsController::class, 'filters']);
        Route::get('notifications', [NotificationController::class, 'index']);
    });
});

