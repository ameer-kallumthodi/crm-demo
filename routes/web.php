<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LeadStatusController;
use App\Http\Controllers\LeadSourceController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TelecallerController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\SettingsController;

// Public routes
Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('custom.auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    
    // Leads
    Route::resource('leads', LeadController::class);
    Route::get('/leads-add', [LeadController::class, 'ajax_add'])->name('leads.add');
    Route::post('/leads-submit', [LeadController::class, 'submit'])->name('leads.submit');
    Route::get('/leads/{lead}/update-status', [LeadController::class, 'updateStatus'])->name('leads.update-status');
    Route::post('/leads/{lead}/update-status', [LeadController::class, 'updateStatus'])->name('leads.update-status.post');
    Route::get('/leads/bulk-reassign', [LeadController::class, 'bulkReassign'])->name('leads.bulk-reassign');
    Route::post('/leads/bulk-reassign', [LeadController::class, 'bulkReassign'])->name('leads.bulk-reassign.post');
    Route::get('/leads/bulk-upload', [LeadController::class, 'bulkUpload'])->name('leads.bulk-upload');
    Route::post('/leads/bulk-upload', [LeadController::class, 'bulkUpload'])->name('leads.bulk-upload.post');
    
    // API routes for AJAX calls
    Route::prefix('api')->group(function () {
        Route::get('/leads/phone', [LeadController::class, 'getByPhone']);
        Route::get('/leads/telecallers-by-team', [LeadController::class, 'getTelecallersByTeam'])->name('leads.telecallers-by-team');
    });
    
        // Admin routes
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('lead-statuses', LeadStatusController::class);
            Route::get('/lead-statuses-add', [LeadStatusController::class, 'ajax_add'])->name('lead-statuses.add');
            Route::get('/lead-statuses-edit/{id}', [LeadStatusController::class, 'ajax_edit'])->name('lead-statuses.edit');
            Route::post('/lead-statuses-submit', [LeadStatusController::class, 'submit'])->name('lead-statuses.submit');
            Route::put('/lead-statuses-update/{id}', [LeadStatusController::class, 'update'])->name('lead-statuses.update');
            Route::get('/lead-statuses-delete/{id}', [LeadStatusController::class, 'delete'])->name('lead-statuses.delete');
            
            Route::resource('lead-sources', LeadSourceController::class);
            Route::get('/lead-sources-add', [LeadSourceController::class, 'ajax_add'])->name('lead-sources.add');
            Route::get('/lead-sources-edit/{id}', [LeadSourceController::class, 'ajax_edit'])->name('lead-sources.edit');
            Route::post('/lead-sources-submit', [LeadSourceController::class, 'submit'])->name('lead-sources.submit');
            Route::put('/lead-sources-update/{id}', [LeadSourceController::class, 'update'])->name('lead-sources.update');
            Route::get('/lead-sources-delete/{id}', [LeadSourceController::class, 'delete'])->name('lead-sources.delete');
            
            Route::resource('countries', CountryController::class);
            Route::get('/countries-add', [CountryController::class, 'ajax_add'])->name('countries.add');
            Route::get('/countries-edit/{id}', [CountryController::class, 'ajax_edit'])->name('countries.edit');
            Route::post('/countries-submit', [CountryController::class, 'submit'])->name('countries.submit');
            Route::put('/countries-update/{id}', [CountryController::class, 'update'])->name('countries.update');
            Route::get('/countries-delete/{id}', [CountryController::class, 'delete'])->name('countries.delete');
            
            Route::resource('courses', CourseController::class);
            Route::get('/courses-add', [CourseController::class, 'ajax_add'])->name('courses.add');
            Route::get('/courses-edit/{id}', [CourseController::class, 'ajax_edit'])->name('courses.edit');
            Route::post('/courses-submit', [CourseController::class, 'submit'])->name('courses.submit');
            Route::put('/courses-update/{id}', [CourseController::class, 'update'])->name('courses.update');
            Route::get('/courses-delete/{id}', [CourseController::class, 'delete'])->name('courses.delete');
            
            Route::resource('teams', TeamController::class);
            Route::get('/teams-add', [TeamController::class, 'ajax_add'])->name('teams.add');
            Route::get('/teams-edit/{id}', [TeamController::class, 'ajax_edit'])->name('teams.edit');
            Route::post('/teams-submit', [TeamController::class, 'submit'])->name('teams.submit');
            Route::put('/teams-update/{id}', [TeamController::class, 'update'])->name('teams.update');
            Route::get('/teams-delete/{id}', [TeamController::class, 'delete'])->name('teams.delete');
            
            Route::resource('telecallers', TelecallerController::class);
            Route::get('/telecallers-add', [TelecallerController::class, 'ajax_add'])->name('telecallers.add');
            Route::get('/telecallers-edit/{id}', [TelecallerController::class, 'ajax_edit'])->name('telecallers.edit');
            Route::post('/telecallers-submit', [TelecallerController::class, 'submit'])->name('telecallers.submit');
            Route::put('/telecallers-update/{id}', [TelecallerController::class, 'update'])->name('telecallers.update');
            Route::get('/telecallers-delete/{id}', [TelecallerController::class, 'delete'])->name('telecallers.delete');
            Route::get('/telecallers-change-password/{id}', [TelecallerController::class, 'changePassword'])->name('telecallers.change-password');
            Route::post('/telecallers-update-password/{id}', [TelecallerController::class, 'updatePassword'])->name('telecallers.update-password');
            
            Route::resource('user-roles', UserRoleController::class);
            Route::resource('settings', SettingsController::class);
        });
});
