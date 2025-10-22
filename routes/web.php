<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LeadStatusController;
use App\Http\Controllers\LeadSourceController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TelecallerController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ConvertedLeadController;
use App\Http\Controllers\VoxbayController;
use App\Http\Controllers\VoxbayCallLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MetaLeadController;

// Public routes
Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Voxbay API routes (no authentication required)
Route::prefix('api/voxbay')->group(function () {
    Route::post('/outgoing-call', [VoxbayController::class, 'outgoingCall'])->name('voxbay.outgoing-call');
    Route::get('/telecaller/{id}/extension', [VoxbayController::class, 'getTelecallerExtension'])->name('voxbay.telecaller.extension');
    Route::get('/test-connection', [VoxbayController::class, 'testConnection'])->name('voxbay.test-connection');
    Route::post('/webhook', [VoxbayController::class, 'webhook'])->name('voxbay.webhook');
});

// Public Meta Leads API routes (no authentication required)
Route::prefix('api/meta-leads')->group(function () {
    Route::get('/fetch', [MetaLeadController::class, 'fetchLeads'])->name('api.meta-leads.fetch');
    Route::get('/push', [MetaLeadController::class, 'pushMetaLeads'])->name('api.meta-leads.push');
    Route::get('/test-token', [MetaLeadController::class, 'testToken'])->name('api.meta-leads.test-token');
    Route::get('/test-original-token', [MetaLeadController::class, 'testOriginalToken'])->name('api.meta-leads.test-original-token');
    Route::get('/try-token-exchange', [MetaLeadController::class, 'tryTokenExchange'])->name('api.meta-leads.try-token-exchange');
    Route::get('/debug-env', [MetaLeadController::class, 'debugEnv'])->name('api.meta-leads.debug-env');
    Route::get('/statistics', [MetaLeadController::class, 'statistics'])->name('api.meta-leads.statistics');
    Route::get('/list', [MetaLeadController::class, 'index'])->name('api.meta-leads.list');
});

// Public Lead Registration Routes
Route::prefix('register')->group(function () {
    // NIOS Registration Routes
    Route::get('/nios/{leadId?}', [App\Http\Controllers\Public\LeadRegistrationController::class, 'showNiosForm'])->name('public.lead.nios.register');
    Route::post('/nios', [App\Http\Controllers\Public\LeadRegistrationController::class, 'store'])->name('public.lead.nios.store');
    Route::get('/nios/subjects', [App\Http\Controllers\Public\LeadRegistrationController::class, 'getSubjects'])->name('public.lead.nios.subjects');
    Route::get('/nios/batches', [App\Http\Controllers\Public\LeadRegistrationController::class, 'getBatches'])->name('public.lead.nios.batches');

    // BOSSE Registration Routes
    Route::get('/bosse/{leadId?}', [App\Http\Controllers\Public\LeadBosseRegistrationController::class, 'showBosseForm'])->name('public.lead.bosse.register');
    Route::post('/bosse', [App\Http\Controllers\Public\LeadBosseRegistrationController::class, 'store'])->name('public.lead.bosse.store');
    Route::get('/bosse/subjects', [App\Http\Controllers\Public\LeadBosseRegistrationController::class, 'getSubjects'])->name('public.lead.bosse.subjects');
    Route::get('/bosse/batches', [App\Http\Controllers\Public\LeadBosseRegistrationController::class, 'getBatches'])->name('public.lead.bosse.batches');

    // GMVSS Registration Routes
    Route::get('/gmvss/{leadId?}', [App\Http\Controllers\Public\LeadGmvssRegistrationController::class, 'showGmvssForm'])->name('public.lead.gmvss.register');
    Route::post('/gmvss', [App\Http\Controllers\Public\LeadGmvssRegistrationController::class, 'store'])->name('public.lead.gmvss.store');
    Route::get('/gmvss/subjects', [App\Http\Controllers\Public\LeadGmvssRegistrationController::class, 'getSubjects'])->name('public.lead.gmvss.subjects');
    Route::get('/gmvss/batches', [App\Http\Controllers\Public\LeadGmvssRegistrationController::class, 'getBatches'])->name('public.lead.gmvss.batches');

    // Medical Coding Registration Routes
    Route::get('/medical-coding/{leadId?}', [App\Http\Controllers\Public\LeadMedicalCodingRegistrationController::class, 'showMedicalCodingForm'])->name('public.lead.medical-coding.register');
    Route::post('/medical-coding', [App\Http\Controllers\Public\LeadMedicalCodingRegistrationController::class, 'store'])->name('public.lead.medical-coding.store');
    Route::get('/medical-coding/{leadId}/success', [App\Http\Controllers\Public\LeadMedicalCodingRegistrationController::class, 'showSuccess'])->name('public.lead.medical-coding.register.success');
    Route::get('/medical-coding/subjects', [App\Http\Controllers\Public\LeadMedicalCodingRegistrationController::class, 'getSubjects'])->name('public.lead.medical-coding.subjects');
    Route::get('/medical-coding/batches', [App\Http\Controllers\Public\LeadMedicalCodingRegistrationController::class, 'getBatches'])->name('public.lead.medical-coding.batches');

    // Hospital Administration Registration Routes
    Route::get('/hospital-admin/{leadId?}', [App\Http\Controllers\Public\LeadHospitalAdminRegistrationController::class, 'showHospitalAdminForm'])->name('public.lead.hospital-admin.register');
    Route::post('/hospital-admin', [App\Http\Controllers\Public\LeadHospitalAdminRegistrationController::class, 'store'])->name('public.lead.hospital-admin.register.store');
    Route::get('/hospital-admin/{leadId}/success', [App\Http\Controllers\Public\LeadHospitalAdminRegistrationController::class, 'showSuccess'])->name('public.lead.hospital-admin.register.success');
    Route::get('/hospital-admin/subjects', [App\Http\Controllers\Public\LeadHospitalAdminRegistrationController::class, 'getSubjects'])->name('public.lead.hospital-admin.subjects');
    Route::get('/hospital-admin/batches', [App\Http\Controllers\Public\LeadHospitalAdminRegistrationController::class, 'getBatches'])->name('public.lead.hospital-admin.batches');

    // E-School Registration Routes
    Route::get('/eschool/{leadId?}', [App\Http\Controllers\Public\LeadESchoolRegistrationController::class, 'showESchoolForm'])->name('public.lead.eschool.register');
    Route::post('/eschool', [App\Http\Controllers\Public\LeadESchoolRegistrationController::class, 'store'])->name('public.lead.eschool.register.store');
    Route::get('/eschool/{leadId}/success', [App\Http\Controllers\Public\LeadESchoolRegistrationController::class, 'showSuccess'])->name('public.lead.eschool.register.success');
    Route::get('/eschool/subjects', [App\Http\Controllers\Public\LeadESchoolRegistrationController::class, 'getSubjects'])->name('public.lead.eschool.subjects');
    Route::get('/eschool/batches', [App\Http\Controllers\Public\LeadESchoolRegistrationController::class, 'getBatches'])->name('public.lead.eschool.batches');

    // Eduthanzeel Registration Routes
    Route::get('/eduthanzeel/{leadId?}', [App\Http\Controllers\Public\LeadEduthanzeelRegistrationController::class, 'showEduthanzeelForm'])->name('public.lead.eduthanzeel.register');
    Route::post('/eduthanzeel', [App\Http\Controllers\Public\LeadEduthanzeelRegistrationController::class, 'store'])->name('public.lead.eduthanzeel.register.store');
    Route::get('/eduthanzeel/{leadId}/success', [App\Http\Controllers\Public\LeadEduthanzeelRegistrationController::class, 'showSuccess'])->name('public.lead.eduthanzeel.register.success');
    Route::get('/eduthanzeel/subjects', [App\Http\Controllers\Public\LeadEduthanzeelRegistrationController::class, 'getSubjects'])->name('public.lead.eduthanzeel.subjects');
    Route::get('/eduthanzeel/batches', [App\Http\Controllers\Public\LeadEduthanzeelRegistrationController::class, 'getBatches'])->name('public.lead.eduthanzeel.batches');

    // TTC Registration Routes
    Route::get('/ttc/{leadId?}', [App\Http\Controllers\Public\LeadTTCRegistrationController::class, 'showTTCForm'])->name('public.lead.ttc.register');
    Route::post('/ttc', [App\Http\Controllers\Public\LeadTTCRegistrationController::class, 'store'])->name('public.lead.ttc.register.store');
    Route::get('/ttc/{leadId}/success', [App\Http\Controllers\Public\LeadTTCRegistrationController::class, 'showSuccess'])->name('public.lead.ttc.register.success');
    Route::get('/ttc/subjects', [App\Http\Controllers\Public\LeadTTCRegistrationController::class, 'getSubjects'])->name('public.lead.ttc.subjects');
    Route::get('/ttc/batches', [App\Http\Controllers\Public\LeadTTCRegistrationController::class, 'getBatches'])->name('public.lead.ttc.batches');

    // Hotel Management Registration Routes
    Route::get('/hotel-mgmt/{leadId?}', [App\Http\Controllers\Public\LeadHotelMgmtRegistrationController::class, 'showHotelMgmtForm'])->name('public.lead.hotel-mgmt.register');
    Route::post('/hotel-mgmt', [App\Http\Controllers\Public\LeadHotelMgmtRegistrationController::class, 'store'])->name('public.lead.hotel-mgmt.register.store');
    Route::get('/hotel-mgmt/{leadId}/success', [App\Http\Controllers\Public\LeadHotelMgmtRegistrationController::class, 'showSuccess'])->name('public.lead.hotel-mgmt.register.success');
    Route::get('/hotel-mgmt/subjects', [App\Http\Controllers\Public\LeadHotelMgmtRegistrationController::class, 'getSubjects'])->name('public.lead.hotel-mgmt.subjects');
    Route::get('/hotel-mgmt/batches', [App\Http\Controllers\Public\LeadHotelMgmtRegistrationController::class, 'getBatches'])->name('public.lead.hotel-mgmt.batches');

    // UG/PG Registration Routes
    Route::get('/ugpg/{leadId?}', [App\Http\Controllers\Public\LeadUGPGRegistrationController::class, 'showUGPGForm'])->name('public.lead.ugpg.register');
    Route::post('/ugpg', [App\Http\Controllers\Public\LeadUGPGRegistrationController::class, 'store'])->name('public.lead.ugpg.register.store');
    Route::get('/ugpg/{leadId}/success', [App\Http\Controllers\Public\LeadUGPGRegistrationController::class, 'showSuccess'])->name('public.lead.ugpg.register.success');
    Route::get('/ugpg/subjects', [App\Http\Controllers\Public\LeadUGPGRegistrationController::class, 'getSubjects'])->name('public.lead.ugpg.subjects');
    Route::get('/ugpg/batches', [App\Http\Controllers\Public\LeadUGPGRegistrationController::class, 'getBatches'])->name('public.lead.ugpg.batches');

    // Python Registration Routes
    Route::get('/python/{leadId?}', [App\Http\Controllers\Public\LeadPythonRegistrationController::class, 'showPythonForm'])->name('public.lead.python.register');
    Route::post('/python', [App\Http\Controllers\Public\LeadPythonRegistrationController::class, 'store'])->name('public.lead.python.register.store');
    Route::get('/python/subjects', [App\Http\Controllers\Public\LeadPythonRegistrationController::class, 'getSubjects'])->name('public.lead.python.subjects');
    Route::get('/python/batches', [App\Http\Controllers\Public\LeadPythonRegistrationController::class, 'getBatches'])->name('public.lead.python.batches');

    // Digital Marketing Registration Routes
    Route::get('/digital-marketing/{leadId?}', [App\Http\Controllers\Public\LeadDigitalMarketingRegistrationController::class, 'showDigitalMarketingForm'])->name('public.lead.digital-marketing.register');
    Route::post('/digital-marketing', [App\Http\Controllers\Public\LeadDigitalMarketingRegistrationController::class, 'store'])->name('public.lead.digital-marketing.register.store');
    Route::get('/digital-marketing/subjects', [App\Http\Controllers\Public\LeadDigitalMarketingRegistrationController::class, 'getSubjects'])->name('public.lead.digital-marketing.subjects');
    Route::get('/digital-marketing/batches', [App\Http\Controllers\Public\LeadDigitalMarketingRegistrationController::class, 'getBatches'])->name('public.lead.digital-marketing.batches');

    // AI Automation Registration Routes
    Route::get('/ai-automation/{leadId?}', [App\Http\Controllers\Public\LeadAIAutomationRegistrationController::class, 'showAIAutomationForm'])->name('public.lead.ai-automation.register');
    Route::post('/ai-automation', [App\Http\Controllers\Public\LeadAIAutomationRegistrationController::class, 'store'])->name('public.lead.ai-automation.register.store');
    Route::get('/ai-automation/subjects', [App\Http\Controllers\Public\LeadAIAutomationRegistrationController::class, 'getSubjects'])->name('public.lead.ai-automation.subjects');
    Route::get('/ai-automation/batches', [App\Http\Controllers\Public\LeadAIAutomationRegistrationController::class, 'getBatches'])->name('public.lead.ai-automation.batches');

    // Web Development & Designing Registration Routes
    Route::get('/web-dev/{leadId?}', [App\Http\Controllers\Public\LeadWebDevRegistrationController::class, 'showWebDevForm'])->name('public.lead.web-dev.register');
    Route::post('/web-dev', [App\Http\Controllers\Public\LeadWebDevRegistrationController::class, 'store'])->name('public.lead.web-dev.register.store');
    Route::get('/web-dev/subjects', [App\Http\Controllers\Public\LeadWebDevRegistrationController::class, 'getSubjects'])->name('public.lead.web-dev.subjects');
    Route::get('/web-dev/batches', [App\Http\Controllers\Public\LeadWebDevRegistrationController::class, 'getBatches'])->name('public.lead.web-dev.batches');

    // Vibe Coding Registration Routes
    Route::get('/vibe-coding/{leadId?}', [App\Http\Controllers\Public\LeadVibeCodingRegistrationController::class, 'showVibeCodingForm'])->name('public.lead.vibe-coding.register');
    Route::post('/vibe-coding', [App\Http\Controllers\Public\LeadVibeCodingRegistrationController::class, 'store'])->name('public.lead.vibe-coding.register.store');
    Route::get('/vibe-coding/subjects', [App\Http\Controllers\Public\LeadVibeCodingRegistrationController::class, 'getSubjects'])->name('public.lead.vibe-coding.subjects');
    Route::get('/vibe-coding/batches', [App\Http\Controllers\Public\LeadVibeCodingRegistrationController::class, 'getBatches'])->name('public.lead.vibe-coding.batches');

    // Graphic Designing Registration Routes
    Route::get('/graphic-designing/{leadId?}', [App\Http\Controllers\Public\LeadGraphicDesigningRegistrationController::class, 'showGraphicDesigningForm'])->name('public.lead.graphic-designing.register');
    Route::post('/graphic-designing', [App\Http\Controllers\Public\LeadGraphicDesigningRegistrationController::class, 'store'])->name('public.lead.graphic-designing.register.store');
    Route::get('/graphic-designing/subjects', [App\Http\Controllers\Public\LeadGraphicDesigningRegistrationController::class, 'getSubjects'])->name('public.lead.graphic-designing.subjects');
    Route::get('/graphic-designing/batches', [App\Http\Controllers\Public\LeadGraphicDesigningRegistrationController::class, 'getBatches'])->name('public.lead.graphic-designing.batches');
});


// Bulk upload form should be protected - moved back to protected routes

// Protected routes
Route::middleware(['custom.auth', 'telecaller.tracking'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');

    // Leads
    Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('followup-leads', [LeadController::class, 'followupLeads'])->name('leads.followup');
    Route::get('/leads-add', [LeadController::class, 'ajax_add'])->name('leads.add');
    Route::post('/leads-submit', [LeadController::class, 'submit'])->name('leads.submit');
    Route::get('/leads/bulk-upload-form', [LeadController::class, 'bulkUploadView'])->name('leads.bulk-upload.test');
    Route::get('/leads/bulk-upload-template', [LeadController::class, 'downloadTemplate'])->name('leads.bulk-upload.template');
    Route::post('/leads/bulk-upload', [LeadController::class, 'bulkUploadSubmit'])->name('leads.bulk-upload.submit');

    // Specific lead routes (must come before generic {lead} route)
    Route::get('leads/{lead}/ajax-show', [LeadController::class, 'ajax_show'])->name('leads.ajax-show');
    Route::get('leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
    Route::get('leads/{lead}/ajax-edit', [LeadController::class, 'ajax_edit'])->name('leads.ajax-edit');
    Route::get('leads/{lead}/status-update', [LeadController::class, 'status_update'])->name('leads.status-update');
    Route::post('leads/{lead}/status-update', [LeadController::class, 'status_update_submit'])->name('leads.status-update-submit');
    Route::get('/leads/{lead}/history', [LeadController::class, 'history'])->name('leads.history');
    Route::get('/leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');
    Route::post('/leads/{lead}/convert', [LeadController::class, 'convertSubmit'])->name('leads.convert.submit');
    Route::get('leads/{lead}/call-logs', [VoxbayCallLogController::class, 'list'])->name('leads.call-logs');
    Route::get('/leads/{lead}/registration-details', [LeadController::class, 'getLeadRegistrationDetails'])->name('leads.registration-details');
    Route::get('/leads/{lead}/approve-modal', [LeadController::class, 'showApproveModal'])->name('leads.approve-modal');
    Route::get('/leads/{lead}/reject-modal', [LeadController::class, 'showRejectModal'])->name('leads.reject-modal');
    Route::post('/leads/{lead}/registration-status', [LeadController::class, 'updateRegistrationStatus'])->name('leads.update-registration-status');

    // Generic lead routes (must come after specific routes)
    Route::get('leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::put('leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::delete('leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');

    // Registration status update route
    Route::post('leads/update-registration-status', [LeadController::class, 'updateRegistrationStatus'])->name('leads.update-lead-registration-status');

    // Document verification route
    Route::post('leads/update-document-verification', [LeadController::class, 'updateDocumentVerification'])->name('leads.update-document-verification');

    // API routes for AJAX calls
    Route::prefix('api')->group(function () {
        Route::get('/leads/phone', [LeadController::class, 'getByPhone']);
        Route::get('/leads/telecallers-by-team', [LeadController::class, 'getTelecallersByTeam'])->name('leads.telecallers-by-team');
        Route::get('/batches/by-course/{courseId}', [App\Http\Controllers\BatchController::class, 'getByCourse'])->name('batches.by-course');
        Route::get('/academic-assistants', [App\Http\Controllers\AcademicAssistantController::class, 'getAll'])->name('academic-assistants.all');

        // Voxbay API routes (duplicates removed - already defined in public routes)

        // Call logs API routes
        Route::get('/call-logs', [VoxbayCallLogController::class, 'ajaxList'])->name('call-logs.ajax-list');
        Route::get('/call-logs/statistics', [VoxbayCallLogController::class, 'statistics'])->name('call-logs.statistics');

        // Telecaller Tracking API routes
        Route::prefix('telecaller-tracking')->group(function () {
            Route::post('/start-idle', [App\Http\Controllers\TelecallerTrackingController::class, 'startIdleTime'])->name('telecaller-tracking.start-idle');
            Route::post('/end-idle', [App\Http\Controllers\TelecallerTrackingController::class, 'endIdleTime'])->name('telecaller-tracking.end-idle');
            Route::post('/sync-idle', [App\Http\Controllers\TelecallerTrackingController::class, 'syncIdleTime'])->name('telecaller-tracking.sync-idle');
            Route::post('/log-activity', [App\Http\Controllers\TelecallerTrackingController::class, 'logActivity'])->name('telecaller-tracking.log-activity');
            Route::get('/current-session', [App\Http\Controllers\TelecallerTrackingController::class, 'getCurrentSession'])->name('telecaller-tracking.current-session');
            Route::post('/auto-logout', [App\Http\Controllers\TelecallerTrackingController::class, 'autoLogout'])->name('telecaller-tracking.auto-logout');
            Route::post('/working-hours-logout', [App\Http\Controllers\TelecallerTrackingController::class, 'workingHoursLogout'])->name('telecaller-tracking.working-hours-logout');
        });

        // API routes for universities
        Route::get('/api/universities/{id}', [UniversityController::class, 'getUniversityData']);

        // API routes for subjects and admission batches
        Route::get('/api/subjects/by-course/{courseId}', [App\Http\Controllers\SubjectController::class, 'getByCourse']);
        Route::get('/api/admission-batches/by-batch/{batchId}', [App\Http\Controllers\AdmissionBatchController::class, 'getByBatch']);
    });

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::delete('/lead-statuses-delete/{id}', [LeadStatusController::class, 'delete'])->name('lead-statuses.delete');
        Route::resource('lead-statuses', LeadStatusController::class);
        Route::get('/lead-statuses-add', [LeadStatusController::class, 'ajax_add'])->name('lead-statuses.add');
        Route::get('/lead-statuses-edit/{id}', [LeadStatusController::class, 'ajax_edit'])->name('lead-statuses.edit');
        Route::post('/lead-statuses-submit', [LeadStatusController::class, 'submit'])->name('lead-statuses.submit');
        Route::put('/lead-statuses-update/{id}', [LeadStatusController::class, 'update'])->name('lead-statuses.update');

        Route::resource('lead-sources', LeadSourceController::class);
        Route::get('/lead-sources-add', [LeadSourceController::class, 'ajax_add'])->name('lead-sources.add');
        Route::get('/lead-sources-edit/{id}', [LeadSourceController::class, 'ajax_edit'])->name('lead-sources.edit');
        Route::post('/lead-sources-submit', [LeadSourceController::class, 'submit'])->name('lead-sources.submit');
        Route::put('/lead-sources-update/{leadSource}', [LeadSourceController::class, 'update'])->name('lead-sources.update');
        Route::delete('/lead-sources-delete/{id}', [LeadSourceController::class, 'delete'])->name('lead-sources.delete');

        Route::resource('universities', UniversityController::class);
        Route::get('/universities-add', [UniversityController::class, 'ajax_add'])->name('universities.add');
        Route::get('/universities-edit/{id}', [UniversityController::class, 'ajax_edit'])->name('universities.edit');
        Route::post('/universities-submit', [UniversityController::class, 'submit'])->name('universities.submit');

        // Registration Links Routes
        Route::resource('registration-links', App\Http\Controllers\RegistrationLinkController::class);
        Route::get('/registration-links-add', [App\Http\Controllers\RegistrationLinkController::class, 'ajax_add'])->name('registration-links.add');
        Route::get('/registration-links-edit/{id}', [App\Http\Controllers\RegistrationLinkController::class, 'ajax_edit'])->name('registration-links.edit');
        Route::post('/registration-links-submit', [App\Http\Controllers\RegistrationLinkController::class, 'submit'])->name('registration-links.submit');
        Route::put('/registration-links-update/{registrationLink}', [App\Http\Controllers\RegistrationLinkController::class, 'update_registration_link'])->name('registration-links.update');
        Route::delete('/registration-links-delete/{id}', [App\Http\Controllers\RegistrationLinkController::class, 'delete'])->name('registration-links.delete');
        Route::put('/universities-update/{university}', [UniversityController::class, 'update'])->name('universities.update');
        Route::delete('/universities-delete/{id}', [UniversityController::class, 'delete'])->name('universities.delete');

        Route::resource('countries', CountryController::class);
        Route::get('/countries-add', [CountryController::class, 'ajax_add'])->name('countries.add');
        Route::get('/countries-edit/{id}', [CountryController::class, 'ajax_edit'])->name('countries.edit');
        Route::post('/countries-submit', [CountryController::class, 'submit'])->name('countries.submit');
        Route::put('/countries-update/{id}', [CountryController::class, 'update'])->name('countries.update');
        Route::delete('/countries-delete/{id}', [CountryController::class, 'delete'])->name('countries.delete');

        Route::delete('/boards-delete/{id}', [App\Http\Controllers\BoardController::class, 'delete'])->name('boards.delete');
        Route::resource('boards', App\Http\Controllers\BoardController::class);
        Route::get('/boards-add', [App\Http\Controllers\BoardController::class, 'ajax_add'])->name('boards.add');
        Route::get('/boards-edit/{id}', [App\Http\Controllers\BoardController::class, 'ajax_edit'])->name('boards.edit');
        Route::post('/boards-submit', [App\Http\Controllers\BoardController::class, 'submit'])->name('boards.submit');
        Route::put('/boards-update/{id}', [App\Http\Controllers\BoardController::class, 'update'])->name('boards.update');

        Route::delete('/batches-delete/{id}', [App\Http\Controllers\BatchController::class, 'delete'])->name('batches.delete');
        Route::resource('batches', App\Http\Controllers\BatchController::class);
        Route::get('/batches-add', [App\Http\Controllers\BatchController::class, 'ajax_add'])->name('batches.add');
        Route::get('/batches-edit/{id}', [App\Http\Controllers\BatchController::class, 'ajax_edit'])->name('batches.edit');
        Route::post('/batches-submit', [App\Http\Controllers\BatchController::class, 'submit'])->name('batches.submit');
        Route::put('/batches-update/{id}', [App\Http\Controllers\BatchController::class, 'update'])->name('batches.update');

        Route::delete('/admission-batches-delete/{id}', [App\Http\Controllers\AdmissionBatchController::class, 'delete'])->name('admission-batches.delete');
        Route::resource('admission-batches', App\Http\Controllers\AdmissionBatchController::class);
        Route::get('/admission-batches-add', [App\Http\Controllers\AdmissionBatchController::class, 'ajax_add'])->name('admission-batches.add');
        Route::get('/admission-batches-edit/{id}', [App\Http\Controllers\AdmissionBatchController::class, 'ajax_edit'])->name('admission-batches.edit');
        Route::post('/admission-batches-submit', [App\Http\Controllers\AdmissionBatchController::class, 'submit'])->name('admission-batches.submit');
        Route::put('/admission-batches-update/{id}', [App\Http\Controllers\AdmissionBatchController::class, 'update'])->name('admission-batches.update');

        Route::resource('courses', CourseController::class);
        Route::get('/courses-add', [CourseController::class, 'ajax_add'])->name('courses.add');
        Route::get('/courses-edit/{id}', [CourseController::class, 'ajax_edit'])->name('courses.edit');
        Route::post('/courses-submit', [CourseController::class, 'submit'])->name('courses.submit');
        Route::put('/courses-update/{id}', [CourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses-delete/{id}', [CourseController::class, 'delete'])->name('courses.delete');

        // Sub Courses Routes (Course-like modal pattern)
        Route::resource('sub-courses', App\Http\Controllers\SubCourseController::class)->except(['create', 'edit', 'update', 'store']);
        Route::get('/sub-courses-add', [App\Http\Controllers\SubCourseController::class, 'ajax_add'])->name('sub-courses.add');
        Route::get('/sub-courses-edit/{id}', [App\Http\Controllers\SubCourseController::class, 'ajax_edit'])->name('sub-courses.edit');
        Route::post('/sub-courses-submit', [App\Http\Controllers\SubCourseController::class, 'submit'])->name('sub-courses.submit');
        Route::put('/sub-courses-update/{id}', [App\Http\Controllers\SubCourseController::class, 'updateForm'])->name('sub-courses.updateForm');

        Route::delete('/subjects-delete/{id}', [App\Http\Controllers\SubjectController::class, 'delete'])->name('subjects.delete');
        Route::resource('subjects', App\Http\Controllers\SubjectController::class);
        Route::get('/subjects-add', [App\Http\Controllers\SubjectController::class, 'ajax_add'])->name('subjects.add');
        Route::get('/subjects-edit/{id}', [App\Http\Controllers\SubjectController::class, 'ajax_edit'])->name('subjects.edit');
        Route::post('/subjects-submit', [App\Http\Controllers\SubjectController::class, 'submit'])->name('subjects.submit');
        Route::put('/subjects-update/{id}', [App\Http\Controllers\SubjectController::class, 'update'])->name('subjects.update');

        Route::resource('teams', TeamController::class);
        Route::get('/teams-add', [TeamController::class, 'ajax_add'])->name('teams.add');
        Route::get('/teams-edit/{id}', [TeamController::class, 'ajax_edit'])->name('teams.edit');
        Route::post('/teams-submit', [TeamController::class, 'submit'])->name('teams.submit');
        Route::put('/teams-update/{id}', [TeamController::class, 'update'])->name('teams.update');
        Route::delete('/teams-delete/{id}', [TeamController::class, 'delete'])->name('teams.delete');
        Route::get('/teams-members/{id}', [TeamController::class, 'members'])->name('teams.members');
        Route::post('/teams-remove-member', [TeamController::class, 'removeMember'])->name('teams.remove-member');
        Route::post('/teams-add-member', [TeamController::class, 'addMember'])->name('teams.add-member');

        Route::resource('telecallers', TelecallerController::class);
        Route::get('/telecallers-add', [TelecallerController::class, 'ajax_add'])->name('telecallers.add');
        Route::get('/telecallers-edit/{id}', [TelecallerController::class, 'ajax_edit'])->name('telecallers.edit');
        Route::post('/telecallers-submit', [TelecallerController::class, 'submit'])->name('telecallers.submit');
        Route::put('/telecallers-update/{id}', [TelecallerController::class, 'update'])->name('telecallers.update');
        Route::delete('/telecallers-delete/{id}', [TelecallerController::class, 'delete'])->name('telecallers.delete');
        Route::get('/telecallers-change-password/{id}', [TelecallerController::class, 'changePassword'])->name('telecallers.change-password');
        Route::post('/telecallers-update-password/{id}', [TelecallerController::class, 'updatePassword'])->name('telecallers.update-password');

        // Teacher routes (role_id = 10)
        Route::resource('teachers', App\Http\Controllers\TeacherController::class);
        Route::get('/teachers-add', [App\Http\Controllers\TeacherController::class, 'ajax_add'])->name('teachers.add');
        Route::get('/teachers-edit/{id}', [App\Http\Controllers\TeacherController::class, 'ajax_edit'])->name('teachers.edit');
        Route::post('/teachers-submit', [App\Http\Controllers\TeacherController::class, 'submit'])->name('teachers.submit');
        Route::put('/teachers-update/{id}', [App\Http\Controllers\TeacherController::class, 'updateForm'])->name('teachers.update-form');

        // Admission Counsellor routes (role_id = 4)
        Route::resource('admission-counsellors', App\Http\Controllers\AdmissionCounsellorController::class);
        Route::get('/admission-counsellors-add', [App\Http\Controllers\AdmissionCounsellorController::class, 'ajax_add'])->name('admission-counsellors.add');
        Route::get('/admission-counsellors-edit/{id}', [App\Http\Controllers\AdmissionCounsellorController::class, 'ajax_edit'])->name('admission-counsellors.edit');
        Route::post('/admission-counsellors-submit', [App\Http\Controllers\AdmissionCounsellorController::class, 'submit'])->name('admission-counsellors.submit');
        Route::put('/admission-counsellors-update/{id}', [App\Http\Controllers\AdmissionCounsellorController::class, 'update'])->name('admission-counsellors.update');
        Route::delete('/admission-counsellors-delete/{id}', [App\Http\Controllers\AdmissionCounsellorController::class, 'delete'])->name('admission-counsellors.delete');
        Route::get('/admission-counsellors-change-password/{id}', [App\Http\Controllers\AdmissionCounsellorController::class, 'changePassword'])->name('admission-counsellors.change-password');
        Route::post('/admission-counsellors-update-password/{id}', [App\Http\Controllers\AdmissionCounsellorController::class, 'updatePassword'])->name('admission-counsellors.update-password');

        // Academic Assistant routes (role_id = 5)
        Route::resource('academic-assistants', App\Http\Controllers\AcademicAssistantController::class);
        Route::get('/academic-assistants-add', [App\Http\Controllers\AcademicAssistantController::class, 'ajax_add'])->name('academic-assistants.add');
        Route::get('/academic-assistants-edit/{id}', [App\Http\Controllers\AcademicAssistantController::class, 'ajax_edit'])->name('academic-assistants.edit');
        Route::post('/academic-assistants-submit', [App\Http\Controllers\AcademicAssistantController::class, 'submit'])->name('academic-assistants.submit');
        Route::put('/academic-assistants-update/{id}', [App\Http\Controllers\AcademicAssistantController::class, 'update'])->name('academic-assistants.update');
        Route::delete('/academic-assistants-delete/{id}', [App\Http\Controllers\AcademicAssistantController::class, 'delete'])->name('academic-assistants.delete');
        Route::get('/academic-assistants-change-password/{id}', [App\Http\Controllers\AcademicAssistantController::class, 'changePassword'])->name('academic-assistants.change-password');
        Route::post('/academic-assistants-update-password/{id}', [App\Http\Controllers\AcademicAssistantController::class, 'updatePassword'])->name('academic-assistants.update-password');

        // Finance routes (role_id = 6)
        Route::resource('finance', App\Http\Controllers\FinanceController::class);
        Route::get('/finance-add', [App\Http\Controllers\FinanceController::class, 'ajax_add'])->name('finance.add');
        Route::get('/finance-edit/{id}', [App\Http\Controllers\FinanceController::class, 'ajax_edit'])->name('finance.edit');
        Route::post('/finance-submit', [App\Http\Controllers\FinanceController::class, 'submit'])->name('finance.submit');
        Route::put('/finance-update/{id}', [App\Http\Controllers\FinanceController::class, 'update'])->name('finance.update');
        Route::delete('/finance-delete/{id}', [App\Http\Controllers\FinanceController::class, 'delete'])->name('finance.delete');
        Route::get('/finance-change-password/{id}', [App\Http\Controllers\FinanceController::class, 'changePassword'])->name('finance.change-password');
        Route::post('/finance-update-password/{id}', [App\Http\Controllers\FinanceController::class, 'updatePassword'])->name('finance.update-password');

        // Support Team routes (role_id = 8)
        Route::resource('support-team', App\Http\Controllers\SupportTeamController::class);
        Route::get('/support-team-add', [App\Http\Controllers\SupportTeamController::class, 'ajax_add'])->name('support-team.add');
        Route::get('/support-team-edit/{id}', [App\Http\Controllers\SupportTeamController::class, 'ajax_edit'])->name('support-team.edit');
        Route::post('/support-team-submit', [App\Http\Controllers\SupportTeamController::class, 'submit'])->name('support-team.submit');
        Route::put('/support-team-update/{id}', [App\Http\Controllers\SupportTeamController::class, 'update'])->name('support-team.update');
        Route::delete('/support-team-delete/{id}', [App\Http\Controllers\SupportTeamController::class, 'delete'])->name('support-team.delete');
        Route::get('/support-team-change-password/{id}', [App\Http\Controllers\SupportTeamController::class, 'changePassword'])->name('support-team.change-password');
        Route::post('/support-team-update-password/{id}', [App\Http\Controllers\SupportTeamController::class, 'updatePassword'])->name('support-team.update-password');

        // Mentor routes (role_id = 9)
        Route::resource('mentor', App\Http\Controllers\MentorController::class);
        Route::get('/mentor-add', [App\Http\Controllers\MentorController::class, 'ajax_add'])->name('mentor.add');
        Route::get('/mentor-edit/{id}', [App\Http\Controllers\MentorController::class, 'ajax_edit'])->name('mentor.edit');
        Route::post('/mentor-submit', [App\Http\Controllers\MentorController::class, 'submit'])->name('mentor.submit');
        Route::put('/mentor-update/{id}', [App\Http\Controllers\MentorController::class, 'update'])->name('mentor.update');
        Route::delete('/mentor-delete/{id}', [App\Http\Controllers\MentorController::class, 'delete'])->name('mentor.delete');
        Route::get('/mentor-change-password/{id}', [App\Http\Controllers\MentorController::class, 'changePassword'])->name('mentor.change-password');
        Route::post('/mentor-update-password/{id}', [App\Http\Controllers\MentorController::class, 'updatePassword'])->name('mentor.update-password');

        // Post-sales routes (role_id = 7)
        Route::resource('post-sales', App\Http\Controllers\PostSalesController::class);
        Route::get('/post-sales-add', [App\Http\Controllers\PostSalesController::class, 'ajax_add'])->name('post-sales.add');
        Route::get('/post-sales-edit/{id}', [App\Http\Controllers\PostSalesController::class, 'ajax_edit'])->name('post-sales.edit');
        Route::post('/post-sales-submit', [App\Http\Controllers\PostSalesController::class, 'submit'])->name('post-sales.submit');
        Route::put('/post-sales-update/{id}', [App\Http\Controllers\PostSalesController::class, 'update'])->name('post-sales.update');
        Route::delete('/post-sales-delete/{id}', [App\Http\Controllers\PostSalesController::class, 'delete'])->name('post-sales.delete');
        Route::get('/post-sales-change-password/{id}', [App\Http\Controllers\PostSalesController::class, 'changePassword'])->name('post-sales.change-password');
        Route::post('/post-sales-update-password/{id}', [App\Http\Controllers\PostSalesController::class, 'updatePassword'])->name('post-sales.update-password');

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

        // Voxbay Call Logs Report routes
        Route::get('/reports/voxbay-call-logs', [App\Http\Controllers\VoxbayReportController::class, 'index'])->name('reports.voxbay-call-logs');
        Route::get('/reports/voxbay-call-logs/export/excel', [App\Http\Controllers\VoxbayReportController::class, 'exportExcel'])->name('reports.voxbay-call-logs.export.excel');
        Route::get('/reports/voxbay-call-logs/export/pdf', [App\Http\Controllers\VoxbayReportController::class, 'exportPdf'])->name('reports.voxbay-call-logs.export.pdf');

        // Course Reports routes
        Route::get('/reports/course-summary', [App\Http\Controllers\CourseReportController::class, 'index'])->name('reports.course-summary');
        Route::get('/reports/course/{courseId}/leads', [App\Http\Controllers\CourseReportController::class, 'courseLeads'])->name('reports.course-leads');
        Route::get('/reports/course/{courseId}/converted-leads', [App\Http\Controllers\CourseReportController::class, 'courseConvertedLeads'])->name('reports.course-converted-leads');
        Route::get('/reports/course-summary/export/excel', [App\Http\Controllers\CourseReportController::class, 'exportCourseSummaryExcel'])->name('reports.course-summary.excel');
        Route::get('/reports/course-summary/export/pdf', [App\Http\Controllers\CourseReportController::class, 'exportCourseSummaryPdf'])->name('reports.course-summary.pdf');

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

        // New Super Admin Reports Routes
        Route::middleware(['super.admin'])->group(function () {
            // Lead Source Efficiency Report
            Route::get('/reports/lead-efficiency', [App\Http\Controllers\LeadEfficiencyReportController::class, 'index'])->name('reports.lead-efficiency');
            Route::get('/reports/lead-efficiency/export/excel', [App\Http\Controllers\LeadEfficiencyReportController::class, 'exportExcel'])->name('reports.lead-efficiency.export.excel');
            Route::get('/reports/lead-efficiency/export/pdf', [App\Http\Controllers\LeadEfficiencyReportController::class, 'exportPdf'])->name('reports.lead-efficiency.export.pdf');

            // Lead Stage Movement Report
            Route::get('/reports/lead-stage-movement', [App\Http\Controllers\LeadStageReportController::class, 'index'])->name('reports.lead-stage-movement');
            Route::get('/reports/lead-stage-movement/export/excel', [App\Http\Controllers\LeadStageReportController::class, 'exportExcel'])->name('reports.lead-stage-movement.export.excel');
            Route::get('/reports/lead-stage-movement/export/pdf', [App\Http\Controllers\LeadStageReportController::class, 'exportPdf'])->name('reports.lead-stage-movement.export.pdf');

            // Lead Aging Report
            Route::get('/reports/lead-aging', [App\Http\Controllers\LeadAgingReportController::class, 'index'])->name('reports.lead-aging');
            Route::get('/reports/lead-aging/export/excel', [App\Http\Controllers\LeadAgingReportController::class, 'exportExcel'])->name('reports.lead-aging.export.excel');
            Route::get('/reports/lead-aging/export/pdf', [App\Http\Controllers\LeadAgingReportController::class, 'exportPdf'])->name('reports.lead-aging.export.pdf');
            Route::get('/reports/lead-detail/{leadId}', [App\Http\Controllers\LeadAgingReportController::class, 'leadDetail'])->name('reports.lead-detail');

            // Team-Wise Detailed Report
            Route::get('/reports/team-wise', [App\Http\Controllers\TeamWiseReportController::class, 'index'])->name('reports.team-wise');
            Route::get('/reports/team-wise/detail', [App\Http\Controllers\TeamWiseReportController::class, 'teamDetail'])->name('reports.team-wise.detail');
            Route::get('/reports/team-wise/export/excel', [App\Http\Controllers\TeamWiseReportController::class, 'export'])->name('reports.team-wise.export');
            Route::get('/reports/team-wise/export/pdf', [App\Http\Controllers\TeamWiseReportController::class, 'exportPdf'])->name('reports.team-wise.export-pdf');
        });

        // Admin Management routes
        Route::get('/admins', [App\Http\Controllers\AdminController::class, 'index'])->name('admins.index');
        Route::get('/admins-add', [App\Http\Controllers\AdminController::class, 'ajax_add'])->name('admins.add');
        Route::get('/admins-edit/{id}', [App\Http\Controllers\AdminController::class, 'ajax_edit'])->name('admins.edit');
        Route::post('/admins-submit', [App\Http\Controllers\AdminController::class, 'submit'])->name('admins.submit');
        Route::put('/admins-update/{id}', [App\Http\Controllers\AdminController::class, 'update'])->name('admins.update');
        Route::delete('/admins-delete/{id}', [App\Http\Controllers\AdminController::class, 'delete'])->name('admins.delete');
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
        Route::get('/converted-leads/{id}/id-card-pdf', [App\Http\Controllers\ConvertedLeadController::class, 'generateIdCardPdf'])->name('converted-leads.id-card-pdf');
        Route::get('/converted-leads/{id}/details-pdf', [App\Http\Controllers\ConvertedLeadController::class, 'generateDetailsPdf'])->name('converted-leads.details-pdf');
        Route::post('/converted-leads/{id}/id-card-generate', [App\Http\Controllers\ConvertedLeadController::class, 'generateAndStoreIdCard'])->name('converted-leads.id-card-generate');
        Route::get('/converted-leads/{id}/id-card', [App\Http\Controllers\ConvertedLeadController::class, 'viewStoredIdCard'])->name('converted-leads.id-card-view');

        // NIOS Converted Leads Routes
        Route::get('/nios-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'niosIndex'])->name('nios-converted-leads.index');

        // BOSSE Converted Leads Routes
        Route::get('/bosse-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'bosseIndex'])->name('bosse-converted-leads.index');

        // Hotel Management Converted Leads Routes
        Route::get('/hotel-management-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'hotelManagementIndex'])->name('hotel-management-converted-leads.index');

        // GMVSS Converted Leads Routes
        Route::get('/gmvss-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'gmvssIndex'])->name('gmvss-converted-leads.index');
        Route::get('/ai-python-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'aiPythonIndex'])->name('ai-python-converted-leads.index');
        Route::get('/digital-marketing-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'digitalMarketingIndex'])->name('digital-marketing-converted-leads.index');
        Route::get('/ai-automation-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'aiAutomationIndex'])->name('ai-automation-converted-leads.index');
        Route::get('/web-development-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'webDevIndex'])->name('web-development-converted-leads.index');
        Route::get('/vibe-coding-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'vibeCodingIndex'])->name('vibe-coding-converted-leads.index');
        Route::get('/graphic-designing-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'graphicDesigningIndex'])->name('graphic-designing-converted-leads.index');
        Route::get('/eduthanzeel-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'eduthanzeelIndex'])->name('eduthanzeel-converted-leads.index');
        Route::get('/e-school-converted-leads', [App\Http\Controllers\ConvertedLeadController::class, 'eschoolIndex'])->name('e-school-converted-leads.index');
        Route::get('/converted-leads/{id}/update-register-number-modal', [App\Http\Controllers\ConvertedLeadController::class, 'showUpdateRegisterNumberModal'])->name('converted-leads.update-register-number-modal');
        Route::post('/converted-leads/{id}/update-register-number', [App\Http\Controllers\ConvertedLeadController::class, 'updateRegisterNumber'])->name('converted-leads.update-register-number');
        Route::post('/converted-leads/{id}/inline-update', [App\Http\Controllers\ConvertedLeadController::class, 'inlineUpdate'])->name('converted-leads.inline-update');

        // BOSSE Mentor Converted Leads Routes
        Route::get('/mentor-bosse-converted-leads', [App\Http\Controllers\MentorConvertedLeadController::class, 'index'])->name('mentor-bosse-converted-leads.index');
        Route::post('/mentor-bosse-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\MentorConvertedLeadController::class, 'updateMentorDetails'])->name('mentor-bosse-converted-leads.update-mentor-details');

        // NIOS Mentor Converted Leads Routes
        Route::get('/mentor-nios-converted-leads', [App\Http\Controllers\NiosMentorConvertedLeadController::class, 'index'])->name('mentor-nios-converted-leads.index');
        Route::post('/mentor-nios-converted-leads/{id}/update-mentor-details', [App\Http\Controllers\NiosMentorConvertedLeadController::class, 'updateMentorDetails'])->name('mentor-nios-converted-leads.update-mentor-details');

        // Invoice Routes
        Route::get('/invoices/student/{studentId}', [App\Http\Controllers\InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{id}', [App\Http\Controllers\InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/create/{studentId}', [App\Http\Controllers\InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices/store/{studentId}', [App\Http\Controllers\InvoiceController::class, 'store'])->name('invoices.store');

        // Payment Routes
        Route::get('/payments/invoice/{invoiceId}', [App\Http\Controllers\PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/create/{invoiceId}', [App\Http\Controllers\PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments/store/{invoiceId}', [App\Http\Controllers\PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{id}', [App\Http\Controllers\PaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{id}/approve', [App\Http\Controllers\PaymentController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{id}/reject', [App\Http\Controllers\PaymentController::class, 'reject'])->name('payments.reject');
        Route::get('/payments/{id}/view', [App\Http\Controllers\PaymentController::class, 'viewFile'])->name('payments.view');
        Route::get('/payments/{id}/download', [App\Http\Controllers\PaymentController::class, 'downloadFile'])->name('payments.download');
        Route::get('/payments/{id}/tax-invoice', [App\Http\Controllers\PaymentController::class, 'taxInvoice'])->name('payments.tax-invoice');
        Route::get('/payments/{id}/tax-invoice-pdf', [App\Http\Controllers\PaymentController::class, 'taxInvoicePdf'])->name('payments.tax-invoice-pdf');
        Route::get('/payments/{id}/payment-receipt', [App\Http\Controllers\PaymentController::class, 'paymentReceipt'])->name('payments.payment-receipt');
        Route::get('/payments/{id}/payment-receipt-pdf', [App\Http\Controllers\PaymentController::class, 'paymentReceiptPdf'])->name('payments.payment-receipt-pdf');

        // Call Logs Routes
        Route::get('/call-logs', [VoxbayCallLogController::class, 'index'])->name('call-logs.index');
        Route::get('/call-logs/{callLog}', [VoxbayCallLogController::class, 'show'])->name('call-logs.show');
        Route::delete('/call-logs/{callLog}', [VoxbayCallLogController::class, 'destroy'])->name('call-logs.destroy');

        // Notifications Routes (Admin only)
        Route::resource('notifications', NotificationController::class);
        Route::get('/notifications/{notification}/show', [NotificationController::class, 'show'])->name('notifications.show');

        // Telecaller Tracking Routes (Super Admin only)
        Route::prefix('telecaller-tracking')->name('telecaller-tracking.')->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\TelecallerReportController::class, 'dashboard'])->name('dashboard');
            Route::get('/reports', [App\Http\Controllers\TelecallerReportController::class, 'reports'])->name('reports');
            Route::get('/reports/{userId}', [App\Http\Controllers\TelecallerReportController::class, 'telecallerReport'])->name('telecaller-report');
            Route::get('/session-details/{sessionId}', [App\Http\Controllers\TelecallerReportController::class, 'sessionDetails'])->name('session-details');
            Route::get('/reports/export/excel', [App\Http\Controllers\TelecallerReportController::class, 'exportExcel'])->name('export.excel');
            Route::get('/reports/export/pdf', [App\Http\Controllers\TelecallerReportController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/realtime-data', [App\Http\Controllers\TelecallerReportController::class, 'getRealtimeData'])->name('realtime-data');
        });

        // Telecaller Task Management Routes (Super Admin only)
        Route::prefix('telecaller-tasks')->name('telecaller-tasks.')->group(function () {
            Route::get('/', [App\Http\Controllers\TelecallerTaskController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\TelecallerTaskController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\TelecallerTaskController::class, 'store'])->name('store');
            Route::get('/overdue', [App\Http\Controllers\TelecallerTaskController::class, 'overdue'])->name('overdue');
            Route::get('/due-today', [App\Http\Controllers\TelecallerTaskController::class, 'dueToday'])->name('due-today');
            Route::get('/statistics', [App\Http\Controllers\TelecallerTaskController::class, 'statistics'])->name('statistics');
            Route::get('/{task}', [App\Http\Controllers\TelecallerTaskController::class, 'show'])->name('show');
            Route::get('/{task}/edit', [App\Http\Controllers\TelecallerTaskController::class, 'edit'])->name('edit');
            Route::put('/{task}', [App\Http\Controllers\TelecallerTaskController::class, 'update'])->name('update');
            Route::post('/{task}/complete', [App\Http\Controllers\TelecallerTaskController::class, 'complete'])->name('complete');
            Route::delete('/{task}', [App\Http\Controllers\TelecallerTaskController::class, 'destroy'])->name('destroy');
        });

        // Meta Leads Admin Routes (Protected)
        Route::prefix('meta-leads')->name('meta-leads.')->group(function () {
            // Main dashboard
            Route::get('/', [MetaLeadController::class, 'index'])->name('index');
            
            // Individual lead operations
            Route::get('/lead/{id}', [MetaLeadController::class, 'show'])->name('show');
            Route::delete('/lead/{id}', [MetaLeadController::class, 'destroy'])->name('destroy');
        });
    });

    // Notification routes for all users
    Route::get('/notifications', [NotificationController::class, 'viewAll'])->name('notifications.view-all');
    Route::get('/api/notifications', [NotificationController::class, 'getUserNotifications'])->name('notifications.api');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
});

// Debug route for Meta leads testing
Route::get('/debug-meta-test', function () {
    try {
        $facebookService = new \App\Services\FacebookApiService();
        $result = $facebookService->fetchLeads();
        
        return response()->json([
            'status' => 'success',
            'environment_check' => [
                'FB_APP_ID' => config('services.facebook.app_id') ? 'SET' : 'NOT SET',
                'FB_APP_SECRET' => config('services.facebook.app_secret') ? 'SET' : 'NOT SET',
                'FB_ACCESS_TOKEN' => config('services.facebook.access_token') ? 'SET' : 'NOT SET',
                'FB_LEAD_FORM_ID' => config('services.facebook.lead_form_id') ? 'SET' : 'NOT SET'
            ],
            'facebook_result' => $result
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Debug routes for idle time calculation
Route::get('/debug-idle-calc', function () {
    $userId = 2;
    $startDate = '2025-09-17';
    $endDate = '2025-09-17';

    // Get idle times for user 2 on 2025-09-17
    $idleTimes = \App\Models\TelecallerIdleTime::where('user_id', $userId)
        ->whereBetween('idle_start_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
        ->get();

    $totalIdleSeconds = $idleTimes->sum('idle_duration_seconds');

    return response()->json([
        'user_id' => $userId,
        'date_range' => [$startDate . ' 00:00:00', $endDate . ' 23:59:59'],
        'idle_times_count' => $idleTimes->count(),
        'idle_times_data' => $idleTimes->pluck('idle_duration_seconds')->toArray(),
        'total_idle_seconds' => $totalIdleSeconds,
        'formatted_time' => gmdate('H:i:s', $totalIdleSeconds)
    ]);
});

Route::get('/debug-telecaller-stats', function () {
    $userId = 2;
    $startDate = '2025-09-17';
    $endDate = '2025-09-17';

    // Simulate the getTelecallerStats method
    $sessions = \App\Models\TelecallerSession::where('user_id', $userId)
        ->whereBetween('login_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
        ->with('idleTimes')
        ->get();

    // If no sessions found in date range, get all sessions for this user (fallback)
    if ($sessions->isEmpty()) {
        $sessions = \App\Models\TelecallerSession::where('user_id', $userId)
            ->with('idleTimes')
            ->get();
    }

    $idleTimes = \App\Models\TelecallerIdleTime::where('user_id', $userId)
        ->whereBetween('idle_start_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
        ->get();

    // If no idle times found in date range, get all idle times for this user (fallback)
    if ($idleTimes->isEmpty()) {
        $idleTimes = \App\Models\TelecallerIdleTime::where('user_id', $userId)->get();
    }

    $totalIdleSeconds = $idleTimes->sum('idle_duration_seconds');

    return response()->json([
        'user_id' => $userId,
        'sessions_count' => $sessions->count(),
        'idle_times_count' => $idleTimes->count(),
        'idle_times_data' => $idleTimes->pluck('idle_duration_seconds')->toArray(),
        'total_idle_seconds' => $totalIdleSeconds,
        'formatted_time' => gmdate('H:i:s', $totalIdleSeconds),
        'sessions_data' => $sessions->map(function ($session) {
            return [
                'id' => $session->id,
                'login_time' => $session->login_time,
                'idle_times_count' => $session->idleTimes->count(),
                'idle_times_sum' => $session->idleTimes->sum('idle_duration_seconds')
            ];
        })
    ]);
});

// Student Verification Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/student/verification/toggle/{studentId}', [App\Http\Controllers\StudentVerificationController::class, 'toggleVerifyStudent'])->name('student.verification.toggle');
    Route::get('/student/verification/status/{studentId}', [App\Http\Controllers\StudentVerificationController::class, 'getVerificationStatus'])->name('student.verification.status');
    Route::post('/student/verification/bulk', [App\Http\Controllers\StudentVerificationController::class, 'bulkVerify'])->name('student.verification.bulk');
});
