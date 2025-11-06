<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadDetail;
use App\Models\LeadStatus;
use App\Models\LeadSource;
use App\Models\Country;
use App\Models\Course;
use App\Models\Team;
use App\Models\User;
use App\Models\LeadActivity;
use App\Models\ConvertedLead;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use App\Exports\LeadsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Facades\Excel;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        // Set execution time limit for this operation
        set_time_limit(config('timeout.max_execution_time', 300));
        
        // ULTRA-OPTIMIZED query - minimal selects and relationships
        $query = Lead::select([
            'id', 'title', 'code', 'phone', 'email', 'lead_status_id', 'lead_source_id', 
            'course_id', 'telecaller_id', 'team_id', 'place', 'rating', 'interest_status', 
            'followup_date', 'remarks', 'is_converted', 'created_at', 'updated_at',
            'gender', 'age', 'whatsapp', 'whatsapp_code', 'qualification', 'country_id', 
            'address' // Added for profile completeness calculation
        ])
        ->where('is_converted', 0) // Direct condition instead of scope for better performance
        ->with([
            'leadStatus:id,title', 
            'leadSource:id,title', 
            'course:id,title', 
            'telecaller:id,name', 
            // Simplified studentDetails - removed nested course eager loading to avoid extra query
            'studentDetails:id,lead_id,status,course_id'
        ]);

        // Apply filters - optimized date range query
        // Only apply date filters if search_key is not provided (to allow searching across all dates)
        $fromDate = $request->get('date_from', now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', now()->format('Y-m-d'));
        
        if (!$request->filled('search_key')) {
            // Use direct whereBetween for better performance than scope
            $query->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        } else {
            // When searching, clear the date values to show search is across all dates
            $fromDate = '';
            $toDate = '';
        }

        if ($request->filled('lead_status_id')) {
            $query->where('lead_status_id', $request->lead_status_id);
        }

        if ($request->filled('lead_source_id')) {
            $query->where('lead_source_id', $request->lead_source_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('telecaller_id')) {
            $query->where('telecaller_id', $request->telecaller_id);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Add search functionality
        if ($request->filled('search_key')) {
            $searchKey = $request->search_key;
            $query->where(function($q) use ($searchKey) {
                $q->where('title', 'LIKE', "%{$searchKey}%")
                  ->orWhere('phone', 'LIKE', "%{$searchKey}%")
                  ->orWhere('email', 'LIKE', "%{$searchKey}%");
            });
        }

        $currentUser = AuthHelper::getCurrentUser();
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        $isGeneralManager = RoleHelper::is_general_manager();
        
        // Role-based lead filtering
        if ($currentUser) {
            // Senior Manager and General Manager: Can see all leads (no filtering)
            if ($isSeniorManager || $isGeneralManager || RoleHelper::is_admin_or_super_admin()) {
                // No filtering - can see all leads
                // Only apply telecaller filter if explicitly requested
                if ($request->filled('telecaller_id')) {
                    $query->where('telecaller_id', $request->telecaller_id);
                }
            } elseif (AuthHelper::isTeamLead() == 1) {
                // Team Lead: Can see their own leads + their team members' leads
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                    // Include current user's ID in the team member IDs
                    $teamMemberIds[] = AuthHelper::getCurrentUserId();  
                    $query->whereIn('telecaller_id', $teamMemberIds);
                } else {
                    // If no team assigned, only show their own leads
                    $query->where('telecaller_id', AuthHelper::getCurrentUserId());
                }
            } elseif (AuthHelper::isTelecaller()) {
                // Telecaller: Can only see their own leads
                $query->where('telecaller_id', AuthHelper::getCurrentUserId());
            }
        }

        // Execute query with optimized ordering
        $startTime = microtime(true);
        $leads = $query->orderBy('id', 'desc')->get();
        $queryTime = microtime(true) - $startTime;

        // Pre-calculate role checks ONCE to avoid repeated calls in view (huge performance gain)
        $isAdminOrSuperAdmin = RoleHelper::is_admin_or_super_admin();
        $isTeamLeadRole = RoleHelper::is_team_lead();
        $isGeneralManager = RoleHelper::is_general_manager();
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        $isTelecallerRole = RoleHelper::is_telecaller();
        $isAcademicAssistant = RoleHelper::is_academic_assistant();
        $isAdmissionCounsellor = RoleHelper::is_admission_counsellor();
        $hasLeadActionPermission = \App\Helpers\PermissionHelper::has_lead_action_permission();

        // OPTIMIZED: Pre-calculate profile data in bulk - faster than each()
        $fieldLabels = [
            'title' => 'Name', 'gender' => 'Gender', 'age' => 'Age', 'phone' => 'Phone',
            'code' => 'Country Code', 'whatsapp' => 'WhatsApp', 'whatsapp_code' => 'WhatsApp Code',
            'email' => 'Email', 'qualification' => 'Qualification', 'country_id' => 'Country',
            'interest_status' => 'Interest Status', 'lead_status_id' => 'Lead Status',
            'lead_source_id' => 'Lead Source', 'address' => 'Address',
            'telecaller_id' => 'Telecaller', 'team_id' => 'Team', 'place' => 'Place'
        ];
        $requiredFields = array_keys($fieldLabels);
        $totalFields = count($requiredFields);
        
        $calcStart = microtime(true);
        foreach ($leads as $lead) {
            // Fast calculation using array access
            $completedFields = 0;
            $missingFields = [];
            
            foreach ($requiredFields as $field) {
                if (!empty($lead->$field)) {
                    $completedFields++;
                } else {
                    $missingFields[] = $fieldLabels[$field];
                }
            }
            
            $profileCompleteness = round(($completedFields / $totalFields) * 100);
            
            // Store as attributes
            $lead->setAttribute('_profile_completeness', $profileCompleteness);
            $lead->setAttribute('_profile_status', $profileCompleteness == 100 ? 'complete' : 
                ($profileCompleteness >= 75 ? 'almost_complete' : 
                ($profileCompleteness >= 50 ? 'partial' : 'incomplete')));
            $lead->setAttribute('_missing_fields', $missingFields);
        }
        $calcTime = microtime(true) - $calcStart;
        
        // Log performance (remove in production)
        if (config('app.debug')) {
            Log::info("Leads Index Performance", [
                'query_time' => round($queryTime, 3),
                'calc_time' => round($calcTime, 3),
                'total_leads' => $leads->count(),
                'total_time' => round($queryTime + $calcTime, 3)
            ]);
        }

        // Get filter options (cached for better performance - cache for 1 hour)
        $leadStatuses = cache()->remember('lead_statuses_list', 3600, function() {
            return LeadStatus::select('id', 'title')->orderBy('title')->get();
        });
        $leadSources = cache()->remember('lead_sources_list', 3600, function() {
            return LeadSource::select('id', 'title')->orderBy('title')->get();
        });
        $countries = cache()->remember('countries_list', 3600, function() {
            return Country::select('id', 'title')->orderBy('title')->get();
        });
        $courses = cache()->remember('courses_list', 3600, function() {
            return Course::select('id', 'title')->orderBy('title')->get();
        });
        
        // Cache telecallers query based on role (reuse $currentUser from above)
        $cacheKey = 'telecallers_list_' . ($currentUser ? $currentUser->id : 'guest');
        $telecallers = cache()->remember($cacheKey, 1800, function() {
            return User::select('id', 'name')
                      ->where('role_id', 3)
                      ->orderBy('name')
                      ->get();
        });

        // Create lookup arrays
        $leadStatusList = $leadStatuses->pluck('title', 'id')->toArray();
        $leadSourceList = $leadSources->pluck('title', 'id')->toArray();
        $courseName = $courses->pluck('title', 'id')->toArray();
        $telecallerList = $telecallers->pluck('name', 'id')->toArray();

        // Get role flags (reuse $currentUser from above)
        $isTelecaller = $currentUser && $currentUser->role_id == 3;
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        
        // Filter telecallers based on role
        if ($isTeamLead) {
            // Team Lead: Show only their team members
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId(); // Include team lead
                $telecallers = User::whereIn('id', $teamMemberIds)->get();
            } else {
                $telecallers = collect([$currentUser]); // Only themselves if no team
            }
        } elseif ($isTelecaller) {
            // Telecaller: Show only themselves
            $telecallers = collect([$currentUser]);
        }
        // Admin/Super Admin: Show all telecallers (already loaded above)
        
        // Update telecallerList after filtering
        $telecallerList = $telecallers->pluck('name', 'id')->toArray();

        return view('admin.leads.index', compact(
            'leads', 'leadStatuses', 'leadSources', 'countries', 'courses', 'telecallers',
            'leadStatusList', 'leadSourceList', 'courseName', 'telecallerList',
            'fromDate', 'toDate', 'isTelecaller', 'isTeamLead',
            'isAdminOrSuperAdmin', 'isTeamLeadRole', 'isGeneralManager', 'isSeniorManager', 'isTelecallerRole',
            'isAcademicAssistant', 'isAdmissionCounsellor', 'hasLeadActionPermission'
        ))->with('search_key', $request->search_key);
    }

    /**
     * Export leads to Excel
     * Uses the same filtering logic as the index method
     */
    public function export(Request $request)
    {
        // Set execution time limit for this operation
        set_time_limit(config('timeout.max_execution_time', 300));
        
        // ULTRA-OPTIMIZED query - minimal selects and relationships (same as index)
        $query = Lead::select([
            'id', 'title', 'code', 'phone', 'email', 'lead_status_id', 'lead_source_id', 
            'course_id', 'telecaller_id', 'team_id', 'place', 'rating', 'interest_status', 
            'followup_date', 'remarks', 'is_converted', 'created_at', 'updated_at'
        ])
        ->where('is_converted', 0)
        ->with([
            'leadStatus:id,title', 
            'leadSource:id,title', 
            'course:id,title', 
            'telecaller:id,name'
        ]);

        // Apply filters - same logic as index method
        $fromDate = $request->get('date_from', now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', now()->format('Y-m-d'));
        
        if (!$request->filled('search_key')) {
            $query->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        }

        if ($request->filled('lead_status_id')) {
            $query->where('lead_status_id', $request->lead_status_id);
        }

        if ($request->filled('lead_source_id')) {
            $query->where('lead_source_id', $request->lead_source_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('telecaller_id')) {
            $query->where('telecaller_id', $request->telecaller_id);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Add search functionality
        if ($request->filled('search_key')) {
            $searchKey = $request->search_key;
            $query->where(function($q) use ($searchKey) {
                $q->where('title', 'LIKE', "%{$searchKey}%")
                  ->orWhere('phone', 'LIKE', "%{$searchKey}%")
                  ->orWhere('email', 'LIKE', "%{$searchKey}%");
            });
        }

        $currentUser = AuthHelper::getCurrentUser();
        
        // Role-based lead filtering (same as index)
        if ($currentUser) {
            if (AuthHelper::isTeamLead() == 1) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                    $teamMemberIds[] = AuthHelper::getCurrentUserId();
                    $query->whereIn('telecaller_id', $teamMemberIds);
                } else {
                    $query->where('telecaller_id', AuthHelper::getCurrentUserId());
                }
            } elseif (AuthHelper::isTelecaller()) {
                $query->where('telecaller_id', AuthHelper::getCurrentUserId());
            } elseif ($request->filled('telecaller_id') && !AuthHelper::isTelecaller()) {
                $query->where('telecaller_id', $request->telecaller_id);
            }
        }

        // Get all leads (no pagination for export)
        $leads = $query->orderBy('id', 'desc')->get();

        // Generate filename with date range
        $filename = 'leads_export_' . ($fromDate ? $fromDate : 'all') . '_to_' . ($toDate ? $toDate : 'all') . '_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new LeadsExport($leads), $filename);
    }

    /**
     * Display registration form submitted leads
     */
    public function registrationFormSubmittedLeads(Request $request)
    {
        // Set execution time limit for this operation
        set_time_limit(config('timeout.max_execution_time', 300));
        
        $query = Lead::select([
            'id', 'title', 'code', 'phone', 'email', 'lead_status_id', 'lead_source_id', 
            'course_id', 'telecaller_id', 'team_id', 'place', 'rating', 'interest_status', 
            'followup_date', 'remarks', 'is_converted', 'created_at', 'updated_at'
        ])
        ->with([
            'leadStatus:id,title', 
            'leadSource:id,title', 
            'course:id,title', 
            'telecaller:id,name', 
            'studentDetails:id,lead_id,status,course_id',
            'leadActivities' => function($query) {
                $query->select('id', 'lead_id', 'reason', 'created_at', 'activity_type')
                      ->whereNotNull('reason')
                      ->where('reason', '!=', '')
                      ->orderBy('created_at', 'desc');
            }
        ])
        ->whereHas('studentDetails') // Only leads that have submitted registration forms
        ->notConverted()
        ->notDropped();

        // Apply filters
        // Only apply date filters if search_key is not provided (to allow searching across all dates)
        $fromDate = $request->get('date_from', now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', now()->format('Y-m-d'));
        
        if (!$request->filled('search_key')) {
            $query->byDateRange($fromDate, $toDate);
        } else {
            // When searching, clear the date values to show search is across all dates
            $fromDate = '';
            $toDate = '';
        }

        if ($request->filled('lead_status_id')) {
            $query->where('lead_status_id', $request->lead_status_id);
        }

        if ($request->filled('lead_source_id')) {
            $query->where('lead_source_id', $request->lead_source_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('telecaller_id')) {
            $query->where('telecaller_id', $request->telecaller_id);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Add registration status filter
        if ($request->filled('registration_status')) {
            $registrationStatus = $request->registration_status;
            if ($registrationStatus === 'approved') {
                $query->whereHas('studentDetails', function($q) {
                    $q->where('status', 'approved');
                });
            } elseif ($registrationStatus === 'rejected') {
                $query->whereHas('studentDetails', function($q) {
                    $q->where('status', 'rejected');
                });
            }
            // If 'all' or empty, no additional filter is applied
        }

        // Add search functionality
        if ($request->filled('search_key')) {
            $searchKey = $request->search_key;
            $query->where(function($q) use ($searchKey) {
                $q->where('title', 'LIKE', "%{$searchKey}%")
                  ->orWhere('phone', 'LIKE', "%{$searchKey}%")
                  ->orWhere('email', 'LIKE', "%{$searchKey}%");
            });
        }

        $currentUser = AuthHelper::getCurrentUser();
        
        // Role-based lead filtering
        if ($currentUser) {
            
             if (AuthHelper::isTeamLead() == 1) {
                
                // Team Lead: Can see their own leads + their team members' leads
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                    // Include current user's ID in the team member IDs
                    $teamMemberIds[] = AuthHelper::getCurrentUserId();  
                    $query->whereIn('telecaller_id', $teamMemberIds);
                } else {
                    // If no team assigned, only show their own leads
                    $query->where('telecaller_id', AuthHelper::getCurrentUserId());
                }
            } elseif (AuthHelper::isTelecaller()) {
                // Telecaller: Can only see their own leads
                $query->where('telecaller_id', AuthHelper::getCurrentUserId());
            }elseif ($request->filled('telecaller_id') && !AuthHelper::isTelecaller()) {
                // Admin/Super Admin: Can filter by specific telecaller
                $query->where('telecaller_id', $request->telecaller_id);
            }
        }

        // Get all leads without pagination
        $leads = $query->orderBy('id', 'desc')->get();

        // Get filter options (optimized with select only needed fields)
        $leadStatuses = LeadStatus::select('id', 'title')->get();
        $leadSources = LeadSource::select('id', 'title')->get();
        $countries = Country::select('id', 'title')->get();
        $courses = Course::select('id', 'title')->get();
        $telecallers = User::select('id', 'name')->where('role_id', 3)->get();

        // Create lookup arrays
        $leadStatusList = $leadStatuses->pluck('title', 'id')->toArray();
        $leadSourceList = $leadSources->pluck('title', 'id')->toArray();
        $courseName = $courses->pluck('title', 'id')->toArray();
        $telecallerList = $telecallers->pluck('name', 'id')->toArray();

        // Get current user for role checking
        $currentUser = AuthHelper::getCurrentUser();
        $isTelecaller = $currentUser && $currentUser->role_id == 3;
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        
        // Filter telecallers based on role
        if ($isTeamLead) {
            // Team Lead: Show only their team members
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId(); // Include team lead
                $telecallers = User::whereIn('id', $teamMemberIds)->get();
            } else {
                $telecallers = collect([$currentUser]); // Only themselves if no team
            }
        } elseif ($isTelecaller) {
            // Telecaller: Show only themselves
            $telecallers = collect([$currentUser]);
        }
        // Admin/Super Admin: Show all telecallers (already loaded above)
        
        // Update telecallerList after filtering
        $telecallerList = $telecallers->pluck('name', 'id')->toArray();

        return view('admin.leads.registration-form-submitted', compact(
            'leads', 'leadStatuses', 'leadSources', 'countries', 'courses', 'telecallers',
            'leadStatusList', 'leadSourceList', 'courseName', 'telecallerList',
            'fromDate', 'toDate', 'isTelecaller', 'isTeamLead'
        ))->with('search_key', $request->search_key);
    }

    /**
     * Display follow-up leads (status = 2)
     */
    public function followupLeads(Request $request)
    {
        $isTelecaller = AuthHelper::isTelecaller();
        $isTeamLead = AuthHelper::isTeamLead();

        // Base query for follow-up leads (status = 2)
        $query = Lead::select([
            'id', 'title', 'code', 'phone', 'email', 'lead_status_id', 'lead_source_id', 
            'course_id', 'telecaller_id', 'team_id', 'place', 'rating', 'interest_status', 
            'followup_date', 'remarks', 'is_converted', 'created_at', 'updated_at'
        ])
        ->with([
            'leadStatus:id,title', 
            'leadSource:id,title', 
            'course:id,title', 
            'telecaller:id,name', 
            'studentDetails:id,lead_id,status,course_id',
            'leadActivities' => function($query) {
                $query->select('id', 'lead_id', 'reason', 'created_at', 'activity_type')
                      ->whereNotNull('reason')
                      ->where('reason', '!=', '')
                      ->orderBy('created_at', 'desc');
            }
        ])
        ->where('lead_status_id', 2)
        ->notConverted()
        ->notDropped();

        // Apply filters
        if ($request->filled('search_key')) {
            $searchTerm = $request->search_key;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('phone', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('lead_source_id')) {
            $query->where('lead_source_id', $request->lead_source_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->filled('telecaller_id')) {
            $query->where('telecaller_id', $request->telecaller_id);
        }

        // Role-based filtering
        if ($isTelecaller && !$isTeamLead) {
            // Telecaller: Can only see their own leads
            $query->where('telecaller_id', AuthHelper::getCurrentUserId());
        } elseif ($isTeamLead) {
            // Team Lead: Can see leads from their team
            $teamId = AuthHelper::getCurrentUser()->team_id ?? null;
            if ($teamId) {
                $query->whereHas('telecaller', function($q) use ($teamId) {
                    $q->where('team_id', $teamId);
                });
            }
            // Admin/Super Admin: Can filter by specific telecaller
            if ($request->filled('telecaller_id')) {
                $query->where('telecaller_id', $request->telecaller_id);
            }
        }

        // Order by follow-up date: current date first, then tomorrow, then future dates, then past dates
        $query->orderByRaw("
            CASE 
                WHEN DATE(followup_date) = CURDATE() THEN 1
                WHEN DATE(followup_date) = DATE_ADD(CURDATE(), INTERVAL 1 DAY) THEN 2
                WHEN DATE(followup_date) > CURDATE() THEN 3
                ELSE 4
            END,
            followup_date ASC
        ");

        // Get all follow-up leads without pagination
        $leads = $query->get();

        // Get filter options (optimized with select only needed fields)
        $leadStatuses = LeadStatus::select('id', 'title')->get();
        $leadSources = LeadSource::select('id', 'title')->get();
        $countries = Country::select('id', 'title')->get();
        $courses = Course::select('id', 'title')->get();
        $telecallers = User::select('id', 'name')->where('role_id', 3)->get();

        return view('admin.leads.followup', compact('leads', 'leadStatuses', 'leadSources', 'countries', 'courses', 'telecallers', 'isTelecaller', 'isTeamLead'));
    }

    public function create()
    {
        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        $isTelecaller = $currentUser && $currentUser->role_id == 3;
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        $isGeneralManager = RoleHelper::is_general_manager();
        
        // Filter telecallers based on role
        if ($isTeamLead) {
            // Team Lead: Show only their team members
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId(); // Include team lead
                $telecallers = User::whereIn('id', $teamMemberIds)->get();
            } else {
                $telecallers = collect([$currentUser]); // Only themselves if no team
            }
        } elseif ($isTelecaller && !$isSeniorManager) {
            // Regular Telecaller: Show only themselves
            $telecallers = collect([$currentUser]);
        } else {
            // Admin/Super Admin/Senior Manager/General Manager: Show all telecallers
            $telecallers = User::where('role_id', 3)->get();
        }
        
        $leadStatuses = LeadStatus::where('is_active', true)->get();
        $leadSources = LeadSource::where('is_active', true)->get();
        $countries = Country::where('is_active', true)->get();
        $courses = Course::where('is_active', true)->get();
        
        // Filter teams based on role
        if ($isTeamLead) {
            // Team Lead: Show only their team
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teams = Team::where('id', $teamId)->get();
            } else {
                $teams = collect(); // No teams if not assigned to any team
            }
        } elseif ($isTelecaller && !$isSeniorManager) {
            // Regular Telecaller: Show only their team
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teams = Team::where('id', $teamId)->get();
            } else {
                $teams = collect(); // No teams if not assigned to any team
            }
        } else {
            // Admin/Super Admin/Senior Manager/General Manager: Show all teams
            $teams = Team::all();
        }
        
        $country_codes = get_country_code();

        return view('admin.leads.create', compact(
            'telecallers', 'leadStatuses', 'leadSources', 'countries', 'courses', 'teams', 'country_codes'
        ));
    }

    public function ajax_add()
    {
        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        $isTelecaller = $currentUser && $currentUser->role_id == 3;
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        $isGeneralManager = RoleHelper::is_general_manager();
        
        // Filter telecallers based on role
        if ($isTeamLead && !$isSeniorManager) {
            // Team Lead: Show only their team members
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId(); // Include team lead
                $telecallers = User::whereIn('id', $teamMemberIds)->get();
            } else {
                $telecallers = collect([$currentUser]); // Only themselves if no team
            }
        } elseif ($isTelecaller && !$isSeniorManager) {
            // Regular Telecaller: Show only themselves
            $telecallers = collect([$currentUser]);
        } else {
            // Admin/Super Admin/Senior Manager/General Manager: Show all telecallers
            $telecallers = User::where('role_id', 3)->get();
        }
        
        $leadStatuses = LeadStatus::where('is_active', true)->get();
        $leadSources = LeadSource::where('is_active', true)->get();
        $countries = Country::where('is_active', true)->get();
        $courses = Course::where('is_active', true)->get();
        
        // Filter teams based on role
        if ($isTeamLead) {
            // Team Lead: Show only their team
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teams = Team::where('id', $teamId)->get();
            } else {
                $teams = collect(); // No teams if not assigned to any team
            }
        } elseif ($isTelecaller && !$isSeniorManager) {
            // Regular Telecaller: Show only their team
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teams = Team::where('id', $teamId)->get();
            } else {
                $teams = collect(); // No teams if not assigned to any team
            }
        } else {
            // Admin/Super Admin/Senior Manager/General Manager: Show all teams
            $teams = Team::all();
        }
        
        $country_codes = get_country_code();

        return view('admin.leads.add', compact(
            'telecallers', 'leadStatuses', 'leadSources', 'countries', 'courses', 'teams', 'country_codes'
        ));
    }

    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email| max:255',
            'code' => 'required|string|max:10',
            'whatsapp_code' => 'nullable|string|max:10',
            'whatsapp' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer|min:1|max:999',
            'place' => 'nullable|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'country_id' => 'nullable|exists:countries,id',
            'course_id' => 'required|exists:courses,id',
            'team_id' => 'nullable|exists:teams,id',
            'telecaller_id' => 'nullable|exists:users,id',
            'address' => 'nullable|string|max:500',
            'followup_date' => 'nullable|date',
            'add_date' => 'nullable|date',
            'add_time' => 'nullable|date_format:H:i',
            'remarks' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        // Check for duplicate lead (code + phone + course_id combination)
        $existingLead = Lead::where('code', $request->code)
            ->where('phone', $request->phone)
            ->where('course_id', $request->course_id)
            ->whereNull('deleted_at')
            ->first();

        if ($existingLead) {
            return redirect()->back()
                ->with('message_danger', 'A lead with this phone number and course combination already exists.')
                ->withInput();
        }

        $leadData = $request->all();
        
        // Set default values
        $leadData['lead_status_id'] = $leadData['lead_status_id'] ?? 1;
        $leadData['add_date'] = $leadData['add_date'] ?? date('Y-m-d');
        $leadData['add_time'] = $leadData['add_time'] ?? date('H:i');
        
        // Get interest_status from lead_status
        $leadStatus = LeadStatus::find($leadData['lead_status_id']);
        $leadData['interest_status'] = $leadStatus ? $leadStatus->interest_status : null;
        
        $lead = Lead::create($leadData);

        return redirect()->route('leads.index')->with('message_success', 'Lead created successfully!');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'code' => 'nullable|string|max:10',
            'whatsapp_code' => 'nullable|string|max:10',
            'whatsapp' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer|min:1|max:999',
            'place' => 'nullable|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'team_id' => 'nullable|exists:teams,id',
            'telecaller_id' => 'nullable|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'address' => 'nullable|string|max:500',
            'followup_date' => 'nullable|date',
            'remarks' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('message_danger', $validator->errors()->first())
                ->withInput();
        }

        // Check for duplicate lead (phone + code + course)
        $existingLead = Lead::where('phone', $request->phone)
                           ->where('code', $request->code)
                           ->where('course_id', $request->course_id)
                           ->whereNull('deleted_at')
                           ->first();

        if ($existingLead) {
            return redirect()->back()
                ->with('message_danger', 'Lead with this phone number and course already exists')
                ->withInput();
        }

        // Get interest_status from lead_status
        $leadStatus = LeadStatus::find($request->lead_status_id);
        $interestStatus = $leadStatus ? $leadStatus->interest_status : null;

        $data = $request->all();
        $data['interest_status'] = $interestStatus; // Override with lead status interest_status
        $data['created_by'] = AuthHelper::getCurrentUserId();
        $data['updated_by'] = AuthHelper::getCurrentUserId();

        $lead = Lead::create($data);

        if ($lead) {
            // Create lead activity
            LeadActivity::create([
                'lead_id' => $lead->id,
                'lead_status_id' => $request->lead_status_id,
                'followup_date' => $request->followup_date,
                'remarks' => $request->remarks,
                'created_by' => AuthHelper::getCurrentUserId(),
                'updated_by' => AuthHelper::getCurrentUserId()
            ]);

            return redirect()->route('leads.index')
                ->with('message_success', 'Lead added successfully!');
        }

        return redirect()->back()
            ->with('message_danger', 'Something went wrong! Please try again.')
            ->withInput();
    }

    public function show(Lead $lead)
    {
        $lead->load([
            'leadStatus', 
            'leadSource', 
            'course', 
            'telecaller', 
            'leadActivities' => function($query) {
                $query->select('id', 'lead_id', 'reason', 'created_at', 'activity_type', 'description', 'remarks', 'rating', 'followup_date', 'created_by', 'lead_status_id')
                      ->with('createdBy:id,name')
                      ->orderBy('created_at', 'desc');
            }
        ]);
        
        $leadStatusList = LeadStatus::pluck('title', 'id')->toArray();
        $leadSourceList = LeadSource::pluck('title', 'id')->toArray();
        $courseName = Course::pluck('title', 'id')->toArray();
        $telecallerList = User::where('role_id', 3)->pluck('name', 'id')->toArray();

        return view('admin.leads.show', compact(
            'lead', 'leadStatusList', 'leadSourceList', 'courseName', 'telecallerList'
        ));
    }

    public function ajax_show(Lead $lead)
    {
        $lead->load([
            'leadStatus', 
            'leadSource', 
            'course', 
            'telecaller', 
            'leadActivities' => function($query) {
                $query->select('id', 'lead_id', 'reason', 'created_at', 'activity_type', 'description', 'remarks', 'rating', 'followup_date', 'created_by')
                      ->with('createdBy:id,name')
                      ->orderBy('created_at', 'desc');
            }
        ]);
        
        return view('admin.leads.show-modal', compact('lead'));
    }

    public function status_update(Lead $lead)
    {
        $leadStatuses = LeadStatus::all();
        $courses = Course::active()->orderBy('title')->get(['id', 'title']);
        $lead->load(['leadActivities' => function($query) {
            $query->with(['leadStatus', 'createdBy'])->orderBy('created_at', 'desc');
        }]);
        return view('admin.leads.status-update-modal', compact('lead', 'leadStatuses', 'courses'));
    }

    public function status_update_submit(Request $request, Lead $lead)
    {
        try {
            // Debug: Log the incoming request data
            Log::info('Status Update Request Data:', $request->all());
            
            // Prepare validation rules
            $rules = [
                'lead_status_id' => 'required|exists:lead_statuses,id',
                'reason' => 'required|string|max:255',
                'remarks' => 'required|string|max:1000',
                'rating' => 'required|integer|min:1|max:10',
                'date' => 'required|date',
                'time' => 'required',
                'course_id' => 'nullable|exists:courses,id',
            ];

            // Only add followup_date validation if status is 2
            if ($request->lead_status_id == 2) {
                $rules['followup_date'] = 'required|date|after_or_equal:today';
            } else {
                $rules['followup_date'] = 'nullable|date';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get current status before updating
            $currentStatus = $lead->leadStatus->title;
            $newStatusId = $request->lead_status_id;
            $newStatus = LeadStatus::find($newStatusId)->title;

            // Get interest_status from new lead_status
            $leadStatus = LeadStatus::find($newStatusId);
            $interestStatus = $leadStatus ? $leadStatus->interest_status : null;

            // Prepare lead update data
            $leadUpdateData = [
                'lead_status_id' => $request->lead_status_id,
                'interest_status' => $interestStatus,
                'rating' => $request->rating,
                'remarks' => $request->remarks,
                'updated_by' => AuthHelper::getCurrentUserId()
            ];
            if ($request->filled('course_id')) {
                $leadUpdateData['course_id'] = $request->course_id;
            }
            
            // If status is 2 (followup), store followup date
            if ($request->lead_status_id == 2 && $request->followup_date) {
                $leadUpdateData['followup_date'] = $request->followup_date;
            }
            
            // Update lead
            $lead->update($leadUpdateData);

            // Generate automatic status change remark
            $statusChangeRemark = "Status changed from '{$currentStatus}' to '{$newStatus}'";
            
            // Combine with user remarks if provided
            $finalRemarks = $statusChangeRemark;
            if (!empty($request->remarks)) {
                $finalRemarks .= " | User Note: " . $request->remarks;
            }

            // Prepare lead activity data
            $activityData = [
                'lead_id' => $lead->id,
                'lead_status_id' => $request->lead_status_id,
                'activity_type' => 'status_update',
                'description' => 'Status updated to ' . $newStatus,
                'followup_date' => $request->date,
                'reason' => $request->reason,
                'rating' => $request->rating,
                'remarks' => $finalRemarks,
                'created_by' => AuthHelper::getCurrentUserId(),
                'updated_by' => AuthHelper::getCurrentUserId(),
            ];
            
            // If status is 2 (followup), store followup date in activity
            if ($request->lead_status_id == 2 && $request->followup_date) {
                $activityData['followup_date'] = $request->followup_date;
            }
            
            // Create lead activity
            LeadActivity::create($activityData);

            return response()->json([
                'success' => true,
                'message' => 'Lead status updated successfully!',
                'data' => $lead->fresh(['leadStatus'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the status. Please try again.'
            ], 500);
        }
    }

    public function edit(Lead $lead)
    {
        // Check edit permission: Admin/Super Admin, General Manager, Team Lead, or Senior Manager only
        // Regular telecallers (without team lead, senior manager, admin, or general manager roles) cannot edit
        $canEditLead = RoleHelper::is_admin_or_super_admin() || 
                       RoleHelper::is_general_manager() || 
                       RoleHelper::is_team_lead() || 
                       RoleHelper::is_senior_manager();
        
        if (!$canEditLead) {
            return redirect()->route('leads.index')
                ->with('error', 'You do not have permission to edit leads.');
        }
        
        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        $isTelecaller = $currentUser && $currentUser->role_id == 3;
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        $isGeneralManager = RoleHelper::is_general_manager();
        
        // Filter telecallers based on role
        if ($isTeamLead && !$isSeniorManager) {
            // Team Lead: Show only their team members
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId(); // Include team lead
                $telecallers = User::whereIn('id', $teamMemberIds)->get();
            } else {
                $telecallers = collect([$currentUser]); // Only themselves if no team
            }
        } elseif ($isTelecaller && !$isSeniorManager) {
            // Regular Telecaller: Show only themselves
            $telecallers = collect([$currentUser]);
        } else {
            // Admin/Super Admin/Senior Manager/General Manager: Show all telecallers
            $telecallers = User::where('role_id', 3)->get();
        }
        
        $leadStatuses = LeadStatus::all();
        $leadSources = LeadSource::all();
        $countries = Country::all();
        $courses = Course::all();
        
        // Filter teams based on role
        if ($isTeamLead && !$isSeniorManager) {
            // Team Lead: Show only their team
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teams = Team::where('id', $teamId)->get();
            } else {
                $teams = collect(); // No teams if not assigned to any team
            }
        } elseif ($isTelecaller && !$isSeniorManager) {
            // Regular Telecaller: Show only their team
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teams = Team::where('id', $teamId)->get();
            } else {
                $teams = collect(); // No teams if not assigned to any team
            }
        } else {
            // Admin/Super Admin/Senior Manager/General Manager: Show all teams
            $teams = Team::all();
        }
        
        $country_codes = get_country_code();

        return view('admin.leads.edit', compact(
            'lead', 'telecallers', 'leadStatuses', 'leadSources', 'countries', 'courses', 'teams', 'country_codes'
        ));
    }

    public function ajax_edit(Lead $lead)
    {
        // Check edit permission: Admin/Super Admin, General Manager, Team Lead, or Senior Manager only
        // Regular telecallers (without team lead, senior manager, admin, or general manager roles) cannot edit
        $canEditLead = RoleHelper::is_admin_or_super_admin() || 
                       RoleHelper::is_general_manager() || 
                       RoleHelper::is_team_lead() || 
                       RoleHelper::is_senior_manager();
        
        if (!$canEditLead) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit leads.'
            ], 403);
        }
        
        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        $isTelecaller = $currentUser && $currentUser->role_id == 3;
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        $isGeneralManager = RoleHelper::is_general_manager();
        
        // Filter telecallers based on role
        if ($isTeamLead && !$isSeniorManager) {
            // Team Lead: Show only their team members
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId(); // Include team lead
                $telecallers = User::whereIn('id', $teamMemberIds)->get();
            } else {
                $telecallers = collect([$currentUser]); // Only themselves if no team
            }
        } elseif ($isTelecaller && !$isSeniorManager) {
            // Regular Telecaller: Show only themselves
            $telecallers = collect([$currentUser]);
        } else {
            // Admin/Super Admin/Senior Manager/General Manager: Show all telecallers
            $telecallers = User::where('role_id', 3)->get();
        }
        
        // Filter teams based on role
        if ($isTeamLead && !$isSeniorManager) {
            // Team Lead: Show only their team
            $teams = Team::where('id', $currentUser->team_id)->get();
        } elseif ($isTelecaller && !$isSeniorManager) {
            // Regular Telecaller: Show only their team (if any)
            $teams = Team::where('id', $currentUser->team_id)->get();
        } else {
            // Admin/Super Admin/Senior Manager/General Manager: Show all teams
            $teams = Team::all();
        }
        
        $leadStatuses = LeadStatus::all();
        $leadSources = LeadSource::all();
        $countries = Country::all();
        $courses = Course::all();
        $country_codes = get_country_code();

        return view('admin.leads.edit-modal', compact(
            'lead', 'telecallers', 'leadStatuses', 'leadSources', 'countries', 'courses', 'teams', 'country_codes', 'isTelecaller', 'isTeamLead'
        ));
    }

    public function destroy(Lead $lead)
    {
        try {
            // Set deleted_by before deleting
            $lead->deleted_by = AuthHelper::getCurrentUserId();
            $lead->save();
            
            $lead->delete();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lead deleted successfully!'
                ]);
            }
            
            return redirect()->route('leads.index')->with('message_success', 'Lead deleted successfully!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the lead. Please try again.'
                ], 500);
            }
            
            return redirect()->back()->with('message_danger', 'An error occurred while deleting the lead. Please try again.');
        }
    }

    public function update(Request $request, Lead $lead)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'nullable|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'nullable|email|max:255',
                'code' => 'required|string|max:10',
                'whatsapp_code' => 'nullable|string|max:10',
                'whatsapp' => 'nullable|string|max:20',
                'gender' => 'nullable|in:male,female,other',
                'age' => 'nullable|integer|min:1|max:999',
                'place' => 'nullable|string|max:255',
                'qualification' => 'nullable|string|max:255',
                'lead_status_id' => 'required|exists:lead_statuses,id',
                'lead_source_id' => 'required|exists:lead_sources,id',
                'country_id' => 'nullable|exists:countries,id',
                'course_id' => 'required|exists:courses,id',
                'team_id' => 'nullable|exists:teams,id',
                'telecaller_id' => 'nullable|exists:users,id',
                'address' => 'nullable|string|max:500',
                'followup_date' => 'nullable|date',
                'add_date' => 'nullable|date',
                'add_time' => 'nullable|date_format:H:i',
                'remarks' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please correct the errors below.',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Check for duplicate lead (phone + code + course, excluding current lead)
            $existingLead = Lead::where('phone', $request->phone)
                               ->where('code', $request->code)
                               ->where('course_id', $request->course_id)
                               ->where('id', '!=', $lead->id)
                               ->whereNull('deleted_at')
                               ->first();

            if ($existingLead) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Lead with this phone number and course already exists'
                    ], 422);
                }
                return redirect()->back()
                    ->with('message_danger', 'Lead with this phone number already exists')
                    ->withInput();
            }

            // Only update fields that are provided in the request
            $data = $request->only([
                'title', 'gender', 'age', 'phone', 'code', 'whatsapp', 'whatsapp_code',
                'email', 'qualification', 'country_id', 'lead_status_id', 'lead_source_id',
                'address', 'telecaller_id', 'team_id', 'place', 'course_id', 'batch_id',
                'university_id', 'followup_date', 'add_date', 'add_time', 'remarks'
            ]);
            
            // Check if telecaller_id is being changed (reassignment)
            $isReassignment = isset($data['telecaller_id']) && 
                             $data['telecaller_id'] != $lead->telecaller_id;
            
            // If reassigning, set lead_status_id to 1
            if ($isReassignment) {
                $data['lead_status_id'] = 1;
            } else {
                // Set default values
                $data['lead_status_id'] = $data['lead_status_id'] ?? 1;
            }
            
            $data['add_date'] = $data['add_date'] ?? date('Y-m-d');
            $data['add_time'] = $data['add_time'] ?? date('H:i');
            
            // Get interest_status from lead_status
            $leadStatus = LeadStatus::find($data['lead_status_id']);
            $data['interest_status'] = $leadStatus ? $leadStatus->interest_status : null;
            
            $data['updated_by'] = AuthHelper::getCurrentUserId();

            // Store old telecaller_id before update
            $oldTelecallerId = $lead->telecaller_id;

            if ($lead->update($data)) {
                // If telecaller was changed, create activity log
                if ($isReassignment && isset($data['telecaller_id'])) {
                    $fromTelecaller = $oldTelecallerId ? \App\Models\User::find($oldTelecallerId) : null;
                    $toTelecaller = \App\Models\User::find($data['telecaller_id']);
                    
                    $fromTelecallerName = $fromTelecaller ? $fromTelecaller->name : 'Unassigned';
                    $toTelecallerName = $toTelecaller ? $toTelecaller->name : 'Unknown';
                    
                    \App\Models\LeadActivity::create([
                        'lead_id' => $lead->id,
                        'lead_status_id' => 1, // Set status to 1 when reassigned
                        'activity_type' => 'reassign',
                        'description' => 'Lead reassigned',
                        'remarks' => "Lead has been reassigned from telecaller {$fromTelecallerName} to telecaller {$toTelecallerName}.",
                        'created_by' => AuthHelper::getCurrentUserId(),
                        'updated_by' => AuthHelper::getCurrentUserId(),
                    ]);
                }
                
                if (request()->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Lead updated successfully!',
                        'data' => $lead
                    ]);
                }
                return redirect()->route('leads.index')
                    ->with('message_success', 'Lead updated successfully!');
            }

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong! Please try again.'
                ], 500);
            }
            return redirect()->back()
                ->with('message_danger', 'Something went wrong! Please try again.')
                ->withInput();
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the lead. Please try again.'
                ], 500);
            }
            return redirect()->back()
                ->with('message_danger', 'An error occurred while updating the lead. Please try again.')
                ->withInput();
        }
    }




    public function bulkUploadView()
    {
        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        
        $leadStatuses = LeadStatus::where('is_active', true)->get();
        $leadSources = LeadSource::where('is_active', true)->get();
        $courses = Course::where('is_active', true)->get();
        
        // Filter teams and telecallers based on role
        if ($isTeamLead && !$isSeniorManager) {
            // Team Lead: Show only their team
            $userTeamId = $currentUser->team_id;
            $teams = Team::where('id', $userTeamId)->where('is_active', true)->get();
            $telecallers = User::where('role_id', 3)
                              ->where('team_id', $userTeamId)
                              ->where('is_active', true)
                              ->get();
        } elseif ($isSeniorManager || RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager()) {
            // Senior Manager/General Manager/Admin/Super Admin: Show all teams and telecallers
            $teams = Team::where('is_active', true)->get();
            $telecallers = User::where('role_id', 3)->where('is_active', true)->get();
        } else {
            // Regular telecaller: Show only their team
            $userTeamId = $currentUser ? $currentUser->team_id : null;
            if ($userTeamId) {
                $teams = Team::where('id', $userTeamId)->where('is_active', true)->get();
                $telecallers = User::where('role_id', 3)
                                  ->where('team_id', $userTeamId)
                                  ->where('is_active', true)
                                  ->get();
            } else {
                $teams = collect();
                $telecallers = collect();
            }
        }
        
        return view('admin.leads.bulk-upload', compact(
            'leadStatuses', 'leadSources', 'courses', 'teams', 'telecallers', 'isSeniorManager'
        ));
    }

    public function downloadTemplate()
    {
        $filePath = storage_path('app/public/lead-sample.xlsx');
        
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Template file not found'], 404);
        }
        
        $currentDateTime = now()->format('Y-m-d_H-i-s');
        $filename = "Lead_Bulk_Upload_{$currentDateTime}.xlsx";
        
        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    public function bulkUploadSubmit(Request $request)
    {
        // Check permission: Admin/Super Admin, General Manager, Team Lead, or Senior Manager only
        $canBulkUpload = RoleHelper::is_admin_or_super_admin() || 
                        RoleHelper::is_general_manager() || 
                        RoleHelper::is_team_lead() || 
                        RoleHelper::is_senior_manager();
        
        if (!$canBulkUpload) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to bulk upload leads.'
            ], 403);
        }

        // Handle POST request - process the bulk upload
        // Set execution time limit for bulk operations
        set_time_limit(config('timeout.max_execution_time', 300));
        ini_set('memory_limit', config('timeout.memory_limit', '256M'));
        
        // Try to set upload limits (may not work on all servers)
        ini_set('upload_max_filesize', '4M');
        ini_set('post_max_size', '8M');
        ini_set('max_input_time', '300');

        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:2048',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'course_id' => 'required|exists:courses,id',
            'team_id' => 'required|string',
            'assign_to_all' => 'boolean',
            'telecallers' => 'required_if:assign_to_all,false|array|min:1',
            'telecallers.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please fix the validation errors.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('excel_file');
            
            // Check if file was uploaded successfully
            if (!$file || !$file->isValid()) {
                $errorMessage = 'File upload failed. ';
                if ($file && $file->getError() === UPLOAD_ERR_INI_SIZE) {
                    $errorMessage .= 'File exceeds server upload limit. Maximum file size: 2MB.';
                } elseif ($file && $file->getError() === UPLOAD_ERR_FORM_SIZE) {
                    $errorMessage .= 'File exceeds form upload limit. Maximum file size: 2MB.';
                } else {
                    $errorMessage .= 'Please check file size and try again. Maximum file size: 2MB.';
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['excel_file' => [$errorMessage]]
                ], 422);
            }
            
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            
            // Check if the worksheet has any data
            if ($highestRow < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Excel file appears to be empty or has no data rows. Please ensure the file contains data starting from row 2.',
                    'errors' => ['excel_file' => ['Excel file is empty or has no data']]
                ], 422);
            }

            // Get telecallers based on assignment type
            if ($request->assign_to_all) {
                // When assigning to all, get telecallers from the selected team or all teams
                if ($request->team_id === 'all') {
                    $telecallers = User::where('role_id', 3)
                        ->where('is_active', true)
                        ->pluck('id')->toArray();
                } else {
                    $telecallers = User::where('team_id', $request->team_id)
                        ->where('role_id', 3)
                        ->where('is_active', true)
                        ->pluck('id')->toArray();
                }
                    
                // Check if team has telecallers
                if (empty($telecallers)) {
                    $message = $request->team_id === 'all' 
                        ? 'No telecallers found in any team. Please assign telecallers manually.'
                        : 'No telecallers found in the selected team. Please select a different team or assign telecallers manually.';
                    
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                        'errors' => ['team_id' => ['No telecallers available']]
                    ], 422);
                }
            } else {
                // When assigning manually, use selected telecallers
                $telecallers = $request->telecallers ?? [];
                
                // Check if telecallers are selected
                if (empty($telecallers)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please select at least one telecaller or choose "Assign to all telecallers in team".',
                        'errors' => ['telecallers' => ['Please select at least one telecaller']]
                    ], 422);
                }
            }

            $telecallerIndex = 0;
            $successCount = 0;
            $duplicateCount = 0;

            // Limit the number of rows to prevent timeout
            $maxRows = min($highestRow, config('timeout.bulk_upload.max_rows', 1000));
            
            for ($row = 2; $row <= $maxRows; $row++) {
                $name = $worksheet->getCell('A' . $row)->getValue();
                $phone = $worksheet->getCell('B' . $row)->getValue();
                $place = $worksheet->getCell('C' . $row)->getValue();
                $remarks = $worksheet->getCell('D' . $row)->getValue();

                if (empty($phone)) continue;

                // Parse phone number to extract country code and phone number using helper
                $phoneData = get_phone_code($phone);
                $code = $phoneData['code'];
                $phoneNumber = $phoneData['phone'];
                
                // If parsing failed, use default country code
                if (empty($code) || empty($phoneNumber)) {
                    $code = '91'; // Default to India
                    $phoneNumber = $phone;
                }

                // Check if lead already exists (check by code, phone, and course)
                $existingLead = Lead::where('phone', $phoneNumber)
                                  ->where('code', $code)
                                  ->where('course_id', $request->course_id)
                                  ->whereNull('deleted_at')
                                  ->first();
                if ($existingLead) {
                    $duplicateCount++;
                    continue;
                }

                // Ensure we have a valid telecaller index
                $telecallerId = $telecallers[$telecallerIndex] ?? $telecallers[0];
                
                // Get interest_status from lead_status
                $leadStatus = LeadStatus::find($request->lead_status_id);
                $interestStatus = $leadStatus ? $leadStatus->interest_status : null;
                
                $lead = Lead::create([
                    'title' => $name,
                    'phone' => $phoneNumber,
                    'code' => $code,
                    'place' => $place,
                    'remarks' => $remarks,
                    'lead_source_id' => $request->lead_source_id,
                    'lead_status_id' => $request->lead_status_id,
                    'interest_status' => $interestStatus,
                    'course_id' => $request->course_id,
                    'telecaller_id' => $telecallerId,
                    'created_by' => AuthHelper::getCurrentUserId(),
                    'updated_by' => AuthHelper::getCurrentUserId(),
                    'is_converted' => false
                ]);

                if ($lead) {
                    $successCount++;
                    
                    // Log activity
                    LeadActivity::create([
                        'lead_id' => $lead->id,
                        'activity_type' => 'bulk_upload',
                        'description' => 'Lead created via bulk upload',
                        'created_by' => AuthHelper::getCurrentUserId(),
                        'created_at' => now()
                    ]);
                    
                    $telecallerIndex = ($telecallerIndex + 1) % count($telecallers);
                }
            }

            $message = "Successfully uploaded {$successCount} leads!";
            if ($duplicateCount > 0) {
                $message .= " {$duplicateCount} duplicates skipped.";
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing file: ' . $e->getMessage()
            ], 500);
        }
    }


    public function statusChange(Request $request, Lead $lead)
    {
        // Handle GET request - return the status change form
        if ($request->isMethod('get')) {
            $leadStatuses = LeadStatus::where('is_active', true)->get();
            return response()->json([
                'success' => true,
                'html' => view('admin.leads.status-change-modal', compact('lead', 'leadStatuses'))->render()
            ]);
        }

        // Handle POST request - process the status change
        $validator = Validator::make($request->all(), [
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'remarks' => 'required|string|max:1000',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        try {
            // Update lead status
            $lead->update([
                'lead_status_id' => $request->lead_status_id,
                'remarks' => $request->remarks,
                'updated_by' => AuthHelper::getCurrentUserId()
            ]);

            // Log activity
            LeadActivity::create([
                'lead_id' => $lead->id,
                'activity_type' => 'status_change',
                'description' => 'Lead status changed to: ' . $lead->leadStatus->title,
                'remarks' => $request->remarks,
                'created_by' => AuthHelper::getCurrentUserId(),
                'created_at' => $request->date . ' ' . $request->time
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead status updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating lead status: ' . $e->getMessage()
            ]);
        }
    }

    public function history(Lead $lead)
    {
        $activities = LeadActivity::where('lead_id', $lead->id)
            ->with(['createdBy:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'html' => view('admin.leads.history-modal', compact('lead', 'activities'))->render()
        ]);
    }

    public function getTelecallersByTeam(Request $request)
    {
        $teamId = $request->get('team_id');
        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        $isTelecaller = $currentUser && $currentUser->role_id == 3;
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        
        if (!$teamId) {
            return response()->json(['telecallers' => []]);
        }

        if ($teamId === 'all') {
            // Get telecallers based on role
            if ($isTeamLead) {
                // Team Lead: Show only their team members
                $userTeamId = $currentUser->team_id;
                if ($userTeamId) {
                    $teamMemberIds = AuthHelper::getTeamMemberIds($userTeamId);
                    
                    $teamMemberIds[] = AuthHelper::getCurrentUserId(); // Include team lead
                    $telecallers = User::whereIn('id', $teamMemberIds)
                                      ->where('is_active', true)
                                      ->with('team:id,name')
                                      ->select('id', 'name', 'email', 'team_id')
                                      ->get();
                } else {
                    $telecallers = collect([$currentUser]); // Only themselves if no team
                }
            } elseif ($isTelecaller && !$isSeniorManager) {
                // Regular Telecaller: Show only themselves
                $telecallers = collect([$currentUser]);
            } else {
                // Admin/Super Admin/Senior Manager: Show all telecallers
                $telecallers = User::where('role_id', 3)
                                  ->where('is_active', true)
                                  ->with('team:id,name')
                                  ->select('id', 'name', 'email', 'team_id')
                                  ->get();
            }
        } else {
            // Get telecallers from specific team (with role filtering)
            $query = User::where('team_id', $teamId)
                        ->where('role_id', 3) // Telecaller role
                        ->where('is_active', true)
                        ->select('id', 'name', 'email');
            
            if ($isTeamLead) {
                // Team Lead: Only show if it's their team
                $userTeamId = $currentUser->team_id;
                if ($teamId != $userTeamId) {
                    $telecallers = collect([]);
                } else {
                    $telecallers = $query->get();
                }
            } elseif ($isTelecaller && !$isSeniorManager) {
                // Regular Telecaller: Only show themselves
                $telecallers = $query->where('id', $currentUser->id)->get();
            } else {
                // Admin/Super Admin/Senior Manager: Show all
                $telecallers = $query->get();
            }
        }

        $telecallers = $telecallers->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'team_name' => $user->team ? $user->team->name : 'No Team'
            ];
        });

        return response()->json(['telecallers' => $telecallers]);
    }

    /**
     * Show bulk reassign form
     */
    public function ajaxBulkReassign()
    {
        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        $isTelecaller = $currentUser && $currentUser->role_id == 3;
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        
        // Filter telecallers based on role
        if ($isTeamLead && !$isSeniorManager) {
            // Team Lead: Show only their team members
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId(); // Include team lead
                $telecallers = User::whereIn('id', $teamMemberIds)->get();
            } else {
                $telecallers = collect([$currentUser]); // Only themselves if no team
            }
        } elseif ($isTelecaller && !$isSeniorManager && !$isTeamLead) {
            // Regular Telecaller: Show only themselves
            $telecallers = collect([$currentUser]);
        } else {
            // Admin/Super Admin/Senior Manager: Show all telecallers
            $telecallers = User::where('role_id', 3)->get();
        }
        
        // No team selection needed for bulk reassign - removed teams
        // Senior managers and admins can see all telecallers
        
        $data = [
            'telecallers' => $telecallers,
            'isSeniorManager' => $isSeniorManager,
            'leadStatuses' => LeadStatus::where('is_active', 1)->get(),
            'leadSources' => LeadSource::where('is_active', 1)->get(),
            'countries' => Country::where('is_active', 1)->get(),
            'courses' => Course::where('is_active', 1)->get(),
        ];

        return view('admin.leads.ajax-bulk-reassign', $data);
    }

    /**
     * Process bulk reassign
     */
    public function bulkReassign(Request $request)
    {
        // Check permission: Admin/Super Admin, General Manager, Team Lead, or Senior Manager only
        $canBulkReassign = RoleHelper::is_admin_or_super_admin() || 
                          RoleHelper::is_general_manager() || 
                          RoleHelper::is_team_lead() || 
                          RoleHelper::is_senior_manager();
        
        if (!$canBulkReassign) {
            return redirect()->back()
                ->with('error', 'You do not have permission to bulk reassign leads.');
        }
        
        $validator = Validator::make($request->all(), [
            'telecaller_id' => 'required|exists:users,id',
            'lead_source_id' => 'required|exists:lead_sources,id',
            // lead_status_id is not required as it's always set to 1 when reassigning
            'from_telecaller_id' => 'required|exists:users,id',
            'lead_from_date' => 'required|date',
            'lead_to_date' => 'required|date',
            'lead_id' => 'required|array|min:1',
            'lead_id.*' => 'exists:leads,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get telecaller names for activity history
        $toTelecaller = \App\Models\User::find($request->telecaller_id);
        $fromTelecaller = \App\Models\User::find($request->from_telecaller_id);
        
        $toTelecallerName = $toTelecaller ? $toTelecaller->name : 'Unknown';
        $fromTelecallerName = $fromTelecaller ? $fromTelecaller->name : 'Unknown';

        $successCount = 0;
        foreach ($request->lead_id as $leadId) {
            // Update the lead directly without loading the full model
            // Set lead_status_id to 1 when reassigning
            $updated = Lead::where('id', $leadId)->update([
                'telecaller_id' => $request->telecaller_id,
                'lead_source_id' => $request->lead_source_id,
                'lead_status_id' => 1, // Always set to 1 when reassigned
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            if ($updated) {
                // Create lead activity history
                \App\Models\LeadActivity::create([
                    'lead_id' => $leadId,
                    'lead_status_id' => 1, // Set status to 1 when reassigned
                    'activity_type' => 'bulk_reassign',
                    'description' => 'Lead reassigned via bulk operation',
                    'remarks' => "Lead has been reassigned from telecaller {$fromTelecallerName} to telecaller {$toTelecallerName}.",
                    'created_by' => AuthHelper::getCurrentUserId(),
                    'updated_by' => AuthHelper::getCurrentUserId(),
                ]);

                $successCount++;
            }
        }

        return redirect()->back()->with('message_success', "Successfully reassigned {$successCount} leads!");
    }

    /**
     * Show bulk delete form
     */
    public function ajaxBulkDelete()
    {
        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && AuthHelper::isTeamLead();
        $isTelecaller = $currentUser && $currentUser->role_id == 3;
        $isSeniorManager = $currentUser && RoleHelper::is_senior_manager();
        
        // Filter telecallers based on role
        if ($isTeamLead) {
            // Team Lead: Show only their team members
            $teamId = $currentUser->team_id;
            if ($teamId) {
                $teamMemberIds = AuthHelper::getTeamMemberIds($teamId);
                $teamMemberIds[] = AuthHelper::getCurrentUserId(); // Include team lead
                $telecallers = User::whereIn('id', $teamMemberIds)->get();
            } else {
                $telecallers = collect([$currentUser]); // Only themselves if no team
            }
        } elseif ($isTelecaller && !$isSeniorManager) {
            // Regular Telecaller: Show only themselves
            $telecallers = collect([$currentUser]);
        } else {
            // Admin/Super Admin/Senior Manager: Show all telecallers
            $telecallers = User::where('role_id', 3)->get();
        }
        
        // No team selection needed for bulk delete - removed teams
        // Senior managers and admins can see all telecallers
        
        $data = [
            'telecallers' => $telecallers,
            'isSeniorManager' => $isSeniorManager,
            'leadStatuses' => LeadStatus::where('is_active', 1)->get(),
            'leadSources' => LeadSource::where('is_active', 1)->get(),
            'countries' => Country::where('is_active', 1)->get(),
            'courses' => Course::where('is_active', 1)->get(),
        ];

        return view('admin.leads.ajax-bulk-delete', $data);
    }

    /**
     * Process bulk delete
     */
    public function bulkDelete(Request $request)
    {
        // Check permission: Admin/Super Admin, General Manager, or Senior Manager only
        $canBulkDelete = RoleHelper::is_admin_or_super_admin() || 
                        RoleHelper::is_general_manager() || 
                        RoleHelper::is_senior_manager();
        
        if (!$canBulkDelete) {
            return redirect()->back()
                ->with('error', 'You do not have permission to bulk delete leads.');
        }
        
        $validator = Validator::make($request->all(), [
            'telecaller_id' => 'required|exists:users,id',
            'lead_date' => 'required|date',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'lead_id' => 'required|array|min:1',
            'lead_id.*' => 'exists:leads,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $successCount = 0;
        foreach ($request->lead_id as $leadId) {
            // Update deleted_by and soft delete directly without loading the full model
            $updated = Lead::where('id', $leadId)->update([
                'deleted_by' => AuthHelper::getCurrentUserId()
            ]);
            
            if ($updated) {
                Lead::where('id', $leadId)->delete();
                $successCount++;
            }
        }

        return redirect()->back()->with('message_success', "Successfully deleted {$successCount} leads!");
    }

    /**
     * Show bulk convert form
     */
    public function ajaxBulkConvert()
    {
        $data = [
            'telecallers' => User::where('role_id', 3)->get(),
            'leadStatuses' => LeadStatus::where('is_active', 1)->get(),
            'leadSources' => LeadSource::where('is_active', 1)->get(),
            'countries' => Country::where('is_active', 1)->get(),
            'courses' => Course::where('is_active', 1)->get(),
        ];

        return view('admin.leads.ajax-bulk-convert', $data);
    }

    /**
     * Process bulk convert
     */
    public function bulkConvert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'telecaller_id' => 'required|exists:users,id',
            'lead_date' => 'required|date',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'lead_id' => 'required|array|min:1',
            'lead_id.*' => 'exists:leads,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $successCount = 0;
        foreach ($request->lead_id as $leadId) {
            $lead = Lead::select(['id', 'title', 'code', 'phone', 'email'])->find($leadId);
            if ($lead) {
                // Get DOB and subject_id from leads_details table
                $leadDetail = \App\Models\LeadDetail::where('lead_id', $leadId)->first();
                $dob = $leadDetail ? $leadDetail->date_of_birth : null;
                $subjectId = $leadDetail ? $leadDetail->subject_id : null;
                
                // Create converted lead record with basic info
                ConvertedLead::create([
                    'lead_id' => $leadId,
                    'name' => $lead->title,
                    'code' => $lead->code,
                    'phone' => $lead->phone,
                    'email' => $lead->email,
                    'dob' => $dob,
                    'subject_id' => $subjectId,
                    'remarks' => $request->remarks ?? 'Converted via bulk operation',
                    'created_by' => AuthHelper::getCurrentUserId(),
                ]);

                // Update lead as converted
                Lead::where('id', $leadId)->update([
                    'is_converted' => 1,
                    'updated_by' => AuthHelper::getCurrentUserId(),
                ]);
                
                $successCount++;
            }
        }

        return redirect()->back()->with('message_success', "Successfully converted {$successCount} leads!");
    }

    /**
     * Get leads by source for bulk operations
     */
    public function getLeadsBySource(Request $request)
    {
        $leads = Lead::select([
            'id', 'title', 'code', 'phone', 'email', 'lead_status_id', 'lead_source_id', 
            'course_id', 'telecaller_id', 'place', 'rating', 'interest_status', 
            'followup_date', 'remarks', 'is_converted', 'created_at'
        ])
        ->where('lead_source_id', $request->lead_source_id)
        ->where('telecaller_id', $request->tele_caller_id)
        ->whereDate('created_at', $request->created_at)
        ->with([
            'leadStatus:id,title', 
            'leadSource:id,title', 
            'telecaller:id,name'
        ])
        ->get();

        return view('admin.leads.partials.leads-table-rows', compact('leads'));
    }

    /**
     * Get leads by source for reassign operations
     */
    public function getLeadsBySourceReassign(Request $request)
    {
        $fromDate = date('Y-m-d H:i:s', strtotime($request->from_date . ' 00:00:00'));
        $toDate = date('Y-m-d H:i:s', strtotime($request->to_date . ' 23:59:59'));
        
        $leads = Lead::select([
            'id', 'title', 'code', 'phone', 'email', 'lead_status_id', 'lead_source_id', 
            'course_id', 'telecaller_id', 'place', 'rating', 'interest_status', 
            'followup_date', 'remarks', 'is_converted', 'created_at'
        ])
        ->where('lead_source_id', $request->lead_source_id)
        ->where('telecaller_id', $request->tele_caller_id)
        ->where('lead_status_id', $request->lead_status_id)
        ->where('created_at', '>=', $fromDate)
        ->where('created_at', '<=', $toDate)
        ->with([
            'leadStatus:id,title', 
            'leadSource:id,title', 
            'telecaller:id,name', 
            'course:id,title'
        ])
        ->get();
        
        return view('admin.leads.partials.leads-table-rows-reassign', compact('leads'));
    }

    /**
     * Show convert lead form
     */
    public function convert(Lead $lead)
    {
        $boards = \App\Models\Board::where('is_active', true)->get();
        $country_codes = get_country_code();
        
        // Load lead details to get DOB and other information
        $lead->load('studentDetails');
        
        // Load the course information if the lead has a course_id
        $course = null;
        $extraAmount = 0;
        $universityAmount = 0;
        $courseType = null;
        $university = null;
        
        if ($lead->course_id) {
            $course = \App\Models\Course::find($lead->course_id);
            
            // Check if it's GMVSS (course_id = 16) and has student details with SSLC class
            if ($lead->course_id == 16 && $lead->studentDetails && $lead->studentDetails->class == 'sslc') {
                $extraAmount = 10000; // ₹10,000 extra for GMVSS SSLC class
            }
            
            // Check if it's UG/PG course (course_id = 9) and has student details with course_type and university
            if ($lead->course_id == 9 && $lead->studentDetails) {
                $courseType = $lead->studentDetails->course_type;
                $universityId = $lead->studentDetails->university_id;
                
                if ($universityId) {
                    $university = \App\Models\University::find($universityId);
                    if ($university) {
                        if ($courseType === 'UG') {
                            $universityAmount = $university->ug_amount ?? 0;
                        } elseif ($courseType === 'PG') {
                            $universityAmount = $university->pg_amount ?? 0;
                        }
                    }
                }
            }
        }

        return view('admin.leads.convert-modal', compact(
            'lead', 'boards', 'country_codes', 'course', 'extraAmount', 'universityAmount', 'courseType', 'university'
        ));
    }

    /**
     * Process lead conversion
     */
    public function convertSubmit(Request $request, Lead $lead)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'dob' => 'nullable|date|before_or_equal:today',
            'board_id' => 'nullable|exists:boards,id',
            'remarks' => 'nullable|string|max:1000',
            'payment_collected' => 'boolean',
            'payment_amount' => 'required_if:payment_collected,1|required_if:payment_collected,true|required_if:payment_collected,"1"|nullable|numeric|min:0.01',
            'payment_type' => 'required_if:payment_collected,1|required_if:payment_collected,true|required_if:payment_collected,"1"|nullable|in:Cash,Online,Bank,Cheque,Card,Other',
            'transaction_id' => 'nullable|string|max:255',
            'payment_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            
            // Get or create lead detail record
            $leadDetail = \App\Models\LeadDetail::where('lead_id', $lead->id)->first();
            if (!$leadDetail) {
                $leadDetail = \App\Models\LeadDetail::create([
                    'lead_id' => $lead->id,
                    'course_id' => $lead->course_id,
                ]);
            }
            
            // Update DOB in lead details if provided
            if ($request->filled('dob')) {
                $leadDetail->update(['date_of_birth' => $request->dob]);
            }
            
            // Get DOB and subject_id for converted lead (from request or existing lead detail)
            $dob = $request->dob ?? ($leadDetail ? $leadDetail->date_of_birth : null);
            $subjectId = $leadDetail ? $leadDetail->subject_id : null;
            
            // Create converted lead record
            $convertedLead = ConvertedLead::create([
                'lead_id' => $lead->id,
                'name' => $request->name,
                'code' => $request->code,
                'phone' => $request->phone,
                'email' => $request->email,
                'dob' => $dob,
                'course_id' => $lead->course_id,
                'batch_id' => $lead->batch_id,
                'board_id' => $request->board_id,
                'subject_id' => $subjectId,
                'candidate_status_id' => 1,
                'remarks' => $request->remarks,
                'created_by' => AuthHelper::getCurrentUserId(),
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            // Update lead as converted
            $lead->update([
                'is_converted' => true,
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            // Auto-generate invoice if lead has course_id
            $invoice = null;
            if ($lead->course_id) {
                $invoiceController = new \App\Http\Controllers\InvoiceController();
                $invoice = $invoiceController->autoGenerate($convertedLead->id, $lead->course_id);
            }

            // Process payment if collected
            if ($request->payment_collected && $invoice) {
                $paymentController = new \App\Http\Controllers\PaymentController();
                $paymentController->autoCreate(
                    $invoice->id,
                    $request->payment_amount,
                    $request->payment_type,
                    $request->transaction_id,
                    $request->file('payment_file')
                );
            }

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lead converted successfully!',
                    'data' => $convertedLead
                ]);
            }

            return redirect()->route('leads.index')
                ->with('message_success', 'Lead converted successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while converting the lead. Please try again.'
                ], 500);
            }

            return redirect()->back()
                ->with('message_danger', 'An error occurred while converting the lead. Please try again.')
                ->withInput();
        }
    }

    /**
     * Get registration details for a lead from leads_details table
     */
    public function getLeadRegistrationDetails(Lead $lead)
    {
        // Check permissions for viewing registration details
        if (!RoleHelper::is_admin_or_super_admin() && 
            !RoleHelper::is_telecaller() && 
            !RoleHelper::is_academic_assistant() && 
            !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('leads.index')->with('message_danger', 'Access denied.');
        }

        try {
            // Load the lead with all necessary relationships
            $lead->load([
                'studentDetails.course', 
                'studentDetails.subject', 
                'studentDetails.batch',
                'studentDetails.subCourse',
                'studentDetails.sslcCertificates',
                'studentDetails.sslcCertificates.verifiedBy',
                'course',
                'leadStatus',
                'leadSource',
                'telecaller',
                'team'
            ]);
            
            if (!$lead->studentDetails) {
                return view('admin.leads.registration-details', compact('lead'))
                    ->with('error', 'No registration details found for this lead.');
            }

            $studentDetail = $lead->studentDetails;
            $country_codes = get_country_code();
            
            // Check if course has sub courses
            // Exclude E-School (course_id = 5) and Eduthanzeel (course_id = 6) from showing sub courses
            $hasSubCourses = false;
            if ($studentDetail->course_id && !in_array($studentDetail->course_id, [5, 6])) {
                $hasSubCourses = \App\Models\SubCourse::where('course_id', $studentDetail->course_id)
                    ->where('is_active', true)
                    ->exists();
            }
            
            return view('admin.leads.registration-details', compact('studentDetail', 'lead', 'country_codes', 'hasSubCourses'));
            
        } catch (\Exception $e) {
            return view('admin.leads.registration-details', compact('lead'))
                ->with('error', 'Error loading registration details: ' . $e->getMessage());
        }
    }

    public function showApproveModal(Lead $lead)
    {
        $studentDetail = $lead->studentDetails;
        if (!$studentDetail) {
            return response('No registration found.', 404);
        }
        return view('admin.leads.partials.approve-modal', compact('lead', 'studentDetail'));
    }

    public function showRejectModal(Lead $lead)
    {
        $studentDetail = $lead->studentDetails;
        if (!$studentDetail) {
            return response('No registration found.', 404);
        }
        return view('admin.leads.partials.reject-modal', compact('lead', 'studentDetail'));
    }

    public function updateRegistrationStatus(Request $request, Lead $lead)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor() && !RoleHelper::is_academic_assistant()) {
            return response()->json(['success' => false, 'message' => 'Access denied'], 403);
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'remark' => 'nullable|string|max:1000'
        ]);

        // Require remark if rejected
        if ($request->status === 'rejected' && !$request->filled('remark')) {
            return response()->json(['success' => false, 'message' => 'Remark is required for rejection.'], 422);
        }

        $studentDetail = $lead->studentDetails;
        if (!$studentDetail) {
            return response()->json(['success' => false, 'message' => 'Registration details not found.'], 404);
        }

        $studentDetail->status = $request->status;
        if ($request->status === 'rejected') {
            $studentDetail->admin_remarks = $request->remark;
        }
        $studentDetail->reviewed_by = AuthHelper::getCurrentUserId();
        $studentDetail->reviewed_at = now();
        $studentDetail->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }

    /**
     * Update document verification status
     */
    public function updateDocumentVerification(Request $request)
    {
        try {
            // Normalize checkbox value before validation
            if ($request->has('need_to_change_document')) {
                $request->merge(['need_to_change_document' => $request->boolean('need_to_change_document')]);
            } else {
                $request->merge(['need_to_change_document' => false]);
            }
            
            $request->validate([
                'lead_detail_id' => 'required|exists:leads_details,id',
                'document_type' => 'required|in:sslc_certificate,plustwo_certificate,plus_two_certificate,ug_certificate,birth_certificate,passport_photo,adhar_front,adhar_back,signature,other_document',
                'verification_status' => 'required|in:pending,verified',
                'need_to_change_document' => 'sometimes|boolean',
                'new_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
            ]);

            $leadDetail = LeadDetail::findOrFail($request->lead_detail_id);
            $documentType = $request->document_type;
            $verificationStatus = $request->verification_status;
            $needToChangeDocument = $request->boolean('need_to_change_document');
            // Use AuthHelper to get the authenticated user
            $currentUserId = AuthHelper::getCurrentUserId();
            //Log the current user
            Log::info('Current user: ' . $currentUserId);
            // Check if user is authenticated
            if (!$currentUserId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated. Please login again.'
                ], 401);
            }

            // If need to change document is checked, file upload is required
            if ($needToChangeDocument && !$request->hasFile('new_file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'File upload is required when "Need to change document" is checked.'
                ], 422);
            }

            // Update verification fields
            // Handle special cases for field mapping
            $fieldMapping = [
                'plustwo_certificate' => 'plustwo',
                'plus_two_certificate' => 'plus_two',
                'birth_certificate' => 'birth_certificate',
                'sslc_certificate' => 'sslc',
                'ug_certificate' => 'ug',
                'passport_photo' => 'passport_photo',
                'adhar_front' => 'adhar_front',
                'adhar_back' => 'adhar_back',
                'signature' => 'signature',
                'other_document' => 'other_document'
            ];
            
            $baseField = $fieldMapping[$documentType] ?? $documentType;
            $verificationField = $baseField . '_verification_status';
            $verifiedByField = $baseField . '_verified_by';
            $verifiedAtField = $baseField . '_verified_at';

            $updateData = [
                $verificationField => $verificationStatus,
                $verifiedByField => $currentUserId,
                $verifiedAtField => now(),
            ];

            // Handle file upload if provided
            if ($request->hasFile('new_file')) {
                $file = $request->file('new_file');
                // Use UUID for file naming to avoid conflicts
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                // Use student-documents directory for consistency with registration forms
                $filePath = $file->storeAs('student-documents', $fileName, 'public');
                
                // Map document type to actual database field
                $fileFieldMapping = [
                    'plustwo_certificate' => 'plustwo_certificate',
                    'plus_two_certificate' => 'plus_two_certificate',
                    'birth_certificate' => 'birth_certificate',
                    'sslc_certificate' => 'sslc_certificate',
                    'ug_certificate' => 'ug_certificate',
                    'passport_photo' => 'passport_photo',
                    'adhar_front' => 'adhar_front',
                    'adhar_back' => 'adhar_back',
                    'signature' => 'signature',
                    'other_document' => 'other_document'
                ];
                
                $fileField = $fileFieldMapping[$documentType] ?? $documentType;
                $updateData[$fileField] = $filePath;
            }

            $leadDetail->update($updateData);

            // Log activity
            LeadActivity::create([
                'lead_id' => $leadDetail->lead_id,
                'activity_type' => 'document_verification',
                'description' => ucfirst(str_replace('_', ' ', $documentType)) . ' verification updated',
                'reason' => "Document: " . ucfirst(str_replace('_', ' ', $documentType)) . 
                           " | Status: " . ucfirst($verificationStatus),
                'created_by' => $currentUserId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document verification updated successfully!',
                'data' => $leadDetail->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating document verification: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifySSLCertificate(Request $request)
    {
        try {
            $request->validate([
                'sslc_certificate_id' => 'required|exists:sslc_certificates,id',
                'lead_detail_id' => 'required|exists:leads_details,id',
                'verification_status' => 'required|in:pending,verified',
                'verification_notes' => 'nullable|string|max:1000',
                'need_to_change_document' => 'nullable|boolean',
                'new_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
            ]);

            $sslcCertificate = \App\Models\SSLCertificate::findOrFail($request->sslc_certificate_id);
            $verificationStatus = $request->verification_status;
            $needToChangeDocument = $request->boolean('need_to_change_document');
            
            // Use AuthHelper to get the authenticated user
            $currentUserId = AuthHelper::getCurrentUserId();
            
            // Check if user is authenticated
            if (!$currentUserId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated. Please login again.'
                ], 401);
            }

            // If need to change document is checked, file upload is required
            if ($needToChangeDocument && !$request->hasFile('new_file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please upload a new document file.'
                ], 400);
            }

            // Handle new file upload if needed
            if ($needToChangeDocument && $request->hasFile('new_file')) {
                // Delete old file
                if (Storage::disk('public')->exists($sslcCertificate->certificate_path)) {
                    Storage::disk('public')->delete($sslcCertificate->certificate_path);
                }
                
                // Upload new file
                $file = $request->file('new_file');
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('student-documents', $fileName, 'public');
                
                // Update certificate with new file
                $sslcCertificate->update([
                    'certificate_path' => $filePath,
                    'original_filename' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                ]);
            }

            // Update verification status
            $updateData = [
                'verification_status' => $verificationStatus,
                'verified_by' => $currentUserId,
                'verified_at' => now(),
            ];

            if ($request->filled('verification_notes')) {
                $updateData['verification_notes'] = $request->verification_notes;
            }

            $sslcCertificate->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'SSLC certificate verification updated successfully.'
            ]);

        } catch (\Exception $e) {
            \Log::error('SSLC certificate verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating SSLC certificate verification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update registration details inline
     */
    public function updateRegistrationDetails(Request $request)
    {
        try {
            $request->validate([
                'lead_detail_id' => 'required|exists:leads_details,id',
                'field' => 'required|string',
                'value' => 'nullable|string|max:255'
            ]);

            $studentDetail = \App\Models\LeadDetail::findOrFail($request->lead_detail_id);
            $field = $request->field;
            $value = $request->value;

            // Define allowed fields for security
            $allowedFields = [
                'student_name', 'father_name', 'mother_name', 'date_of_birth', 'gender',
                'email', 'phone', 'whatsapp', 'street', 'locality', 'post_office', 'district', 'state', 'pin_code',
                'message', 'subject_id', 'batch_id', 'sub_course_id'
            ];

            if (!in_array($field, $allowedFields)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid field for editing.'
                ], 400);
            }

            // Handle phone fields specially
            if (in_array($field, ['phone', 'whatsapp', 'parents_phone'])) {
                if (strpos($value, '|') !== false) {
                    [$code, $number] = explode('|', $value, 2);
                    
                    if ($field === 'phone') {
                        $studentDetail->update([
                            'personal_code' => $code,
                            'personal_number' => $number
                        ]);
                    } elseif ($field === 'whatsapp') {
                        $studentDetail->update([
                            'whatsapp_code' => $code,
                            'whatsapp_number' => $number
                        ]);
                    } elseif ($field === 'parents_phone') {
                        $studentDetail->update([
                            'parents_code' => $code,
                            'parents_number' => $number
                        ]);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid phone number format.'
                    ], 400);
                }
            } elseif (in_array($field, ['subject_id', 'batch_id', 'sub_course_id'])) {
                // Handle ID fields - validate they exist and belong to the course
                $value = $value ? (int)$value : null;
                
                if ($field === 'subject_id' && $value) {
                    $subject = \App\Models\Subject::where('id', $value)
                        ->where('course_id', $studentDetail->course_id)
                        ->first();
                    if (!$subject) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid subject selected.'
                        ], 400);
                    }
                } elseif ($field === 'batch_id' && $value) {
                    $batch = \App\Models\Batch::where('id', $value)
                        ->where('course_id', $studentDetail->course_id)
                        ->first();
                    if (!$batch) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid batch selected.'
                        ], 400);
                    }
                } elseif ($field === 'sub_course_id' && $value) {
                    $subCourse = \App\Models\SubCourse::where('id', $value)
                        ->where('course_id', $studentDetail->course_id)
                        ->first();
                    if (!$subCourse) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid sub course selected.'
                        ], 400);
                    }
                }
                
                $studentDetail->update([$field => $value]);
                
                // Reload relationships to get updated values
                $studentDetail->load('subject', 'batch', 'subCourse');
                $newValue = null;
                if ($field === 'subject_id') {
                    $newValue = $studentDetail->subject->title ?? 'N/A';
                } elseif ($field === 'batch_id') {
                    $newValue = $studentDetail->batch->title ?? 'N/A';
                } elseif ($field === 'sub_course_id') {
                    $newValue = $studentDetail->subCourse->title ?? 'N/A';
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Registration details updated successfully.',
                    'new_value' => $newValue,
                    'updated_id' => $value
                ]);
            } else {
                $studentDetail->update([$field => $value]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Registration details updated successfully.',
                    'new_value' => $value
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Registration details update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating registration details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove SSLC certificate
     */
    public function removeSSLCertificate(Request $request)
    {
        try {
            $request->validate([
                'certificate_id' => 'required|exists:sslc_certificates,id'
            ]);

            $certificate = \App\Models\SSLCertificate::findOrFail($request->certificate_id);
            
            // Delete the file from storage
            if (Storage::disk('public')->exists($certificate->certificate_path)) {
                Storage::disk('public')->delete($certificate->certificate_path);
            }
            
            // Delete the database record
            $certificate->delete();

            return response()->json([
                'success' => true,
                'message' => 'SSLC certificate removed successfully.'
            ]);

        } catch (\Exception $e) {
            \Log::error('SSLC certificate removal error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error removing SSLC certificate: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add new SSLC certificate
     */
    public function addSSLCCertificates(Request $request)
    {
        try {
            $request->validate([
                'lead_detail_id' => 'required|exists:leads_details,id',
                'certificates' => 'required|array|min:1',
                'certificates.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'
            ]);

            $leadDetailId = $request->lead_detail_id;
            $certificateIds = [];

            foreach ($request->file('certificates') as $file) {
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('student-documents', $fileName, 'public');

                // Create SSLC certificate record
                $sslcCertificate = \App\Models\SSLCertificate::create([
                    'lead_detail_id' => $leadDetailId,
                    'certificate_path' => $filePath,
                    'original_filename' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                    'verification_status' => 'pending',
                ]);

                $certificateIds[] = $sslcCertificate->id;
            }

            return response()->json([
                'success' => true,
                'message' => 'SSLC certificate(s) added successfully.',
                'certificate_ids' => $certificateIds
            ]);

        } catch (\Exception $e) {
            \Log::error('SSLC certificate addition error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error adding SSLC certificate: ' . $e->getMessage()
            ], 500);
        }
    }

}