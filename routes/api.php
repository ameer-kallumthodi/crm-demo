<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\LeadsController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\MarketingLeadsController;

Route::prefix('v1')->group(function() {
    Route::post('auth/login', [AuthController::class, 'login']);
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function() {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('home', [HomeController::class, 'index']);
        Route::get('marketing-home', [HomeController::class, 'marketingHome']);
        Route::get('leads', [LeadsController::class, 'index']);
        Route::get('leads/filters', [LeadsController::class, 'filters']);
        Route::get('leads/call', [LeadsController::class, 'callLead']);
        Route::get('notifications', [NotificationController::class, 'index']);
        
        // Marketing Leads APIs
        Route::get('marketing-leads', [MarketingLeadsController::class, 'index']);
        Route::post('marketing-leads', [MarketingLeadsController::class, 'store']);
        Route::get('marketing-leads/d2d-form-messages', [MarketingLeadsController::class, 'd2dFormMessages']);
        Route::get('marketing-leads/form-data', [MarketingLeadsController::class, 'formData']);
    });
});

