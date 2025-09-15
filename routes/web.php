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
use App\Http\Controllers\ConvertedLeadController;
use App\Http\Controllers\VoxbayController;
use App\Http\Controllers\VoxbayCallLogController;
use App\Http\Controllers\NotificationController;

// Public routes
Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Bulk upload form should be protected - moved back to protected routes

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
    Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('/leads-add', [LeadController::class, 'ajax_add'])->name('leads.add');
    Route::post('/leads-submit', [LeadController::class, 'submit'])->name('leads.submit');
    Route::get('/leads/bulk-upload-form', [LeadController::class, 'bulkUploadView'])->name('leads.bulk-upload.test');
    Route::get('/leads/bulk-upload-template', [LeadController::class, 'downloadTemplate'])->name('leads.bulk-upload.template');
    Route::post('/leads/bulk-upload', [LeadController::class, 'bulkUploadSubmit'])->name('leads.bulk-upload.submit');
    Route::get('leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::get('leads/{lead}/ajax-show', [LeadController::class, 'ajax_show'])->name('leads.ajax-show');
    Route::get('leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
    Route::get('leads/{lead}/ajax-edit', [LeadController::class, 'ajax_edit'])->name('leads.ajax-edit');
    Route::get('leads/{lead}/delete', [LeadController::class, 'delete'])->name('leads.delete');
    Route::get('leads/{lead}/status-update', [LeadController::class, 'status_update'])->name('leads.status-update');
    Route::post('leads/{lead}/status-update', [LeadController::class, 'status_update_submit'])->name('leads.status-update-submit');
    Route::put('leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::delete('leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
    Route::get('/leads/{lead}/history', [LeadController::class, 'history'])->name('leads.history');
    Route::get('/leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');
    Route::post('/leads/{lead}/convert', [LeadController::class, 'convertSubmit'])->name('leads.convert.submit');
    Route::get('leads/{lead}/call-logs', [VoxbayCallLogController::class, 'list'])->name('leads.call-logs');
    
    // API routes for AJAX calls
    Route::prefix('api')->group(function () {
        Route::get('/leads/phone', [LeadController::class, 'getByPhone']);
        Route::get('/leads/telecallers-by-team', [LeadController::class, 'getTelecallersByTeam'])->name('leads.telecallers-by-team');
        
        // Voxbay API routes
        Route::post('/voxbay/outgoing-call', [VoxbayController::class, 'outgoingCall'])->name('voxbay.outgoing-call');
        Route::get('/voxbay/telecaller/{id}/extension', [VoxbayController::class, 'getTelecallerExtension'])->name('voxbay.telecaller.extension');
        Route::get('/voxbay/test-connection', [VoxbayController::class, 'testConnection'])->name('voxbay.test-connection');
        
        // Call logs API routes
        Route::get('/call-logs', [VoxbayCallLogController::class, 'ajaxList'])->name('call-logs.ajax-list');
        Route::get('/call-logs/statistics', [VoxbayCallLogController::class, 'statistics'])->name('call-logs.statistics');
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
            Route::put('/lead-sources-update/{leadSource}', [LeadSourceController::class, 'update'])->name('lead-sources.update');
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
            Route::get('/teams-members/{id}', [TeamController::class, 'members'])->name('teams.members');
            Route::post('/teams-remove-member', [TeamController::class, 'removeMember'])->name('teams.remove-member');
            Route::post('/teams-add-member', [TeamController::class, 'addMember'])->name('teams.add-member');
            
            Route::resource('telecallers', TelecallerController::class);
            Route::get('/telecallers-add', [TelecallerController::class, 'ajax_add'])->name('telecallers.add');
            Route::get('/telecallers-edit/{id}', [TelecallerController::class, 'ajax_edit'])->name('telecallers.edit');
            Route::post('/telecallers-submit', [TelecallerController::class, 'submit'])->name('telecallers.submit');
            Route::put('/telecallers-update/{id}', [TelecallerController::class, 'update'])->name('telecallers.update');
            Route::get('/telecallers-delete/{id}', [TelecallerController::class, 'delete'])->name('telecallers.delete');
            Route::get('/telecallers-change-password/{id}', [TelecallerController::class, 'changePassword'])->name('telecallers.change-password');
            Route::post('/telecallers-update-password/{id}', [TelecallerController::class, 'updatePassword'])->name('telecallers.update-password');
            
            // Admission Counsellor routes (role_id = 4)
            Route::resource('admission-counsellors', App\Http\Controllers\AdmissionCounsellorController::class);
            Route::get('/admission-counsellors-add', [App\Http\Controllers\AdmissionCounsellorController::class, 'ajax_add'])->name('admission-counsellors.add');
            Route::get('/admission-counsellors-edit/{id}', [App\Http\Controllers\AdmissionCounsellorController::class, 'ajax_edit'])->name('admission-counsellors.edit');
            Route::post('/admission-counsellors-submit', [App\Http\Controllers\AdmissionCounsellorController::class, 'submit'])->name('admission-counsellors.submit');
            Route::put('/admission-counsellors-update/{id}', [App\Http\Controllers\AdmissionCounsellorController::class, 'update'])->name('admission-counsellors.update');
            Route::get('/admission-counsellors-delete/{id}', [App\Http\Controllers\AdmissionCounsellorController::class, 'delete'])->name('admission-counsellors.delete');
            Route::get('/admission-counsellors-change-password/{id}', [App\Http\Controllers\AdmissionCounsellorController::class, 'changePassword'])->name('admission-counsellors.change-password');
            Route::post('/admission-counsellors-update-password/{id}', [App\Http\Controllers\AdmissionCounsellorController::class, 'updatePassword'])->name('admission-counsellors.update-password');
            
            // Academic Assistant routes (role_id = 5)
            Route::resource('academic-assistants', App\Http\Controllers\AcademicAssistantController::class);
            Route::get('/academic-assistants-add', [App\Http\Controllers\AcademicAssistantController::class, 'ajax_add'])->name('academic-assistants.add');
            Route::get('/academic-assistants-edit/{id}', [App\Http\Controllers\AcademicAssistantController::class, 'ajax_edit'])->name('academic-assistants.edit');
            Route::post('/academic-assistants-submit', [App\Http\Controllers\AcademicAssistantController::class, 'submit'])->name('academic-assistants.submit');
            Route::put('/academic-assistants-update/{id}', [App\Http\Controllers\AcademicAssistantController::class, 'update'])->name('academic-assistants.update');
            Route::get('/academic-assistants-delete/{id}', [App\Http\Controllers\AcademicAssistantController::class, 'delete'])->name('academic-assistants.delete');
            Route::get('/academic-assistants-change-password/{id}', [App\Http\Controllers\AcademicAssistantController::class, 'changePassword'])->name('academic-assistants.change-password');
            Route::post('/academic-assistants-update-password/{id}', [App\Http\Controllers\AcademicAssistantController::class, 'updatePassword'])->name('academic-assistants.update-password');
            
            Route::resource('user-roles', UserRoleController::class);
            Route::resource('settings', SettingsController::class);
            
            // Website Settings routes (must be after resource routes to avoid conflicts)
            Route::get('/website-settings', [App\Http\Controllers\SettingController::class, 'index'])->name('website.settings');
            Route::post('/settings/update-logo', [App\Http\Controllers\SettingController::class, 'updateLogo'])->name('website.settings.update-logo');
            Route::post('/settings/update-favicon', [App\Http\Controllers\SettingController::class, 'updateFavicon'])->name('website.settings.update-favicon');
            Route::post('/settings/update-site-settings', [App\Http\Controllers\SettingController::class, 'updateSiteSettings'])->name('website.settings.update-site-settings');
            Route::post('/settings/update-bg-image', [App\Http\Controllers\SettingController::class, 'updateBackgroundImage'])->name('website.settings.update-bg-image');
            Route::post('/settings/remove-bg-image', [App\Http\Controllers\SettingController::class, 'removeBackgroundImage'])->name('website.settings.remove-bg-image');
            
            // Reports routes
            Route::get('/reports/leads', [App\Http\Controllers\LeadReportController::class, 'index'])->name('reports.leads');
            Route::get('/reports/lead-status', [App\Http\Controllers\LeadReportController::class, 'leadStatusReport'])->name('reports.lead-status');
            Route::get('/reports/lead-source', [App\Http\Controllers\LeadReportController::class, 'leadSourceReport'])->name('reports.lead-source');
            Route::get('/reports/team', [App\Http\Controllers\LeadReportController::class, 'teamReport'])->name('reports.team');
            Route::get('/reports/telecaller', [App\Http\Controllers\LeadReportController::class, 'telecallerReport'])->name('reports.telecaller');
            
            // Export routes
            Route::get('/reports/lead-status/export/excel', [App\Http\Controllers\LeadReportController::class, 'exportLeadStatusExcel'])->name('reports.lead-status.excel');
            Route::get('/reports/lead-status/export/pdf', [App\Http\Controllers\LeadReportController::class, 'exportLeadStatusPdf'])->name('reports.lead-status.pdf');
            Route::get('/reports/lead-source/export/excel', [App\Http\Controllers\LeadReportController::class, 'exportLeadSourceExcel'])->name('reports.lead-source.excel');
            Route::get('/reports/lead-source/export/pdf', [App\Http\Controllers\LeadReportController::class, 'exportLeadSourcePdf'])->name('reports.lead-source.pdf');
            Route::get('/reports/team/export/excel', [App\Http\Controllers\LeadReportController::class, 'exportTeamExcel'])->name('reports.team.excel');
            Route::get('/reports/team/export/pdf', [App\Http\Controllers\LeadReportController::class, 'exportTeamPdf'])->name('reports.team.pdf');
            Route::get('/reports/telecaller/export/excel', [App\Http\Controllers\LeadReportController::class, 'exportTelecallerExcel'])->name('reports.telecaller.excel');
            Route::get('/reports/telecaller/export/pdf', [App\Http\Controllers\LeadReportController::class, 'exportTelecallerPdf'])->name('reports.telecaller.pdf');
            Route::get('/reports/export/excel', [App\Http\Controllers\LeadReportController::class, 'exportMainReportsExcel'])->name('reports.main.excel');
            Route::get('/reports/export/pdf', [App\Http\Controllers\LeadReportController::class, 'exportMainReportsPdf'])->name('reports.main.pdf');
            
            // Admin Management routes
            Route::get('/admins', [App\Http\Controllers\AdminController::class, 'index'])->name('admins.index');
            Route::get('/admins-add', [App\Http\Controllers\AdminController::class, 'ajax_add'])->name('admins.add');
            Route::get('/admins-edit/{id}', [App\Http\Controllers\AdminController::class, 'ajax_edit'])->name('admins.edit');
            Route::post('/admins-submit', [App\Http\Controllers\AdminController::class, 'submit'])->name('admins.submit');
            Route::put('/admins-update/{id}', [App\Http\Controllers\AdminController::class, 'update'])->name('admins.update');
            Route::get('/admins-delete/{id}', [App\Http\Controllers\AdminController::class, 'delete'])->name('admins.delete');
            Route::delete('/admins-destroy/{id}', [App\Http\Controllers\AdminController::class, 'destroy'])->name('admins.destroy');
            Route::get('/admins-change-password/{id}', [App\Http\Controllers\AdminController::class, 'changePassword'])->name('admins.change-password');
            Route::post('/admins-update-password/{id}', [App\Http\Controllers\AdminController::class, 'updatePassword'])->name('admins.update-password');
            
            // Bulk Operations Routes
            Route::get('/leads/bulk-reassign', [App\Http\Controllers\LeadController::class, 'ajaxBulkReassign'])->name('leads.bulk-reassign');
            Route::post('/leads/bulk-reassign', [App\Http\Controllers\LeadController::class, 'bulkReassign'])->name('leads.bulk-reassign.submit');
            Route::get('/leads/bulk-delete', [App\Http\Controllers\LeadController::class, 'ajaxBulkDelete'])->name('leads.bulk-delete');
            Route::post('/leads/bulk-delete', [App\Http\Controllers\LeadController::class, 'bulkDelete'])->name('leads.bulk-delete.submit');
            Route::get('/leads/bulk-convert', [App\Http\Controllers\LeadController::class, 'ajaxBulkConvert'])->name('leads.bulk-convert');
            Route::post('/leads/bulk-convert', [App\Http\Controllers\LeadController::class, 'bulkConvert'])->name('leads.bulk-convert.submit');
            
            // AJAX routes for bulk operations
            Route::post('/leads/get-leads-by-source', [App\Http\Controllers\LeadController::class, 'getLeadsBySource'])->name('leads.get-by-source');
            Route::post('/leads/get-leads-by-source-reassign', [App\Http\Controllers\LeadController::class, 'getLeadsBySourceReassign'])->name('leads.get-by-source-reassign');
            
            // Converted Leads Routes
            Route::get('/converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'index'])->name('converted-leads.index');
            Route::get('/converted-leads/view/{id}', [App\Http\Controllers\ConvertedLeadController::class, 'show'])->name('converted-leads.show');
            
            // Call Logs Routes
            Route::get('/call-logs', [VoxbayCallLogController::class, 'index'])->name('call-logs.index');
            Route::get('/call-logs/{callLog}', [VoxbayCallLogController::class, 'show'])->name('call-logs.show');
            Route::delete('/call-logs/{callLog}', [VoxbayCallLogController::class, 'destroy'])->name('call-logs.destroy');
            
            // Notifications Routes (Admin only)
            Route::resource('notifications', NotificationController::class);
            Route::get('/notifications/{notification}/show', [NotificationController::class, 'show'])->name('notifications.show');
            
        });
        
        // Notification routes for all users
        Route::get('/notifications', [NotificationController::class, 'viewAll'])->name('notifications.view-all');
        Route::get('/api/notifications', [NotificationController::class, 'getUserNotifications'])->name('notifications.api');
        Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
});
