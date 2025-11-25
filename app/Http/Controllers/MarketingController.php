<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRole;
use App\Models\Team;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\LeadSource;
use App\Models\LeadActivity;
use App\Models\Course;
use App\Models\MarketingLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Helpers\RoleHelper;
use App\Helpers\AuthHelper;

class MarketingController extends Controller
{
    public function index()
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager())) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $marketings = User::where('role_id', 13)->with(['role', 'team'])->get();
        $roles = UserRole::all();
        $teams = Team::where('marketing_team', true)->get();
        
        return view('admin.marketing.index', compact('marketings', 'roles', 'teams'));
    }

    public function store(Request $request)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager())) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:10',
            'password' => 'required|string|min:6',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        // Verify team is a marketing team
        if ($request->team_id) {
            $team = Team::find($request->team_id);
            if (!$team || !$team->marketing_team) {
                return response()->json([
                    'error' => 'Selected team must be a marketing team.'
                ], 422);
            }
        }

        // Filter only the fields we need
        $data = $request->only(['name', 'email', 'phone', 'code', 'password', 'team_id']);
        $data['password'] = Hash::make($data['password']);
        $data['role_id'] = 13; // Static role for Marketing

        $marketing = User::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Marketing user created successfully.',
            'data' => $marketing->load('role', 'team')
        ]);
    }

    public function show(User $marketing)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager())) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($marketing->load('role', 'team'));
    }


    public function destroy(User $marketing)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager())) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $marketing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Marketing user deleted successfully.'
        ]);
    }


    public function ajax_add(Request $request)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager())) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $teams = Team::where('marketing_team', true)->get();
        $country_codes = get_country_code();
        $selectedTeamId = $request->get('team_id');
        
        return view('admin.marketing.add', compact('teams', 'country_codes', 'selectedTeamId'));
    }

    public function submit(Request $request)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager())) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:10',
            'password' => 'required|string|min:6',
            'team_id' => 'nullable|exists:teams,id',
            'is_team_lead' => 'nullable|boolean',
            'joining_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        // Verify team is a marketing team
        if ($request->team_id) {
            $team = Team::find($request->team_id);
            if (!$team || !$team->marketing_team) {
                return redirect()->back()->with('message_danger', 'Selected team must be a marketing team.')->withInput();
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'code' => $request->code,
            'password' => Hash::make($request->password),
            'role_id' => 13, // Static role for Marketing
            'team_id' => $request->team_id,
            'is_team_lead' => $request->has('is_team_lead') ? 1 : 0,
            'joining_date' => $request->joining_date,
        ]);

        // If user is marked as team lead and has a team, set them as team lead
        if ($request->has('is_team_lead') && $request->team_id) {
            // First, remove any existing team lead from this team
            $existingTeamLead = Team::where('id', $request->team_id)->first();
            if ($existingTeamLead && $existingTeamLead->team_lead_id) {
                User::where('id', $existingTeamLead->team_lead_id)->update(['is_team_lead' => 0]);
            }
            
            // Set the new team lead
            Team::where('id', $request->team_id)->update(['team_lead_id' => $user->id]);
        }

        return redirect()->route('admin.marketing.index')->with('message_success', 'Marketing user created successfully!');
    }

    public function ajax_edit($id)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager())) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = User::findOrFail($id);
        $teams = Team::where('marketing_team', true)->get();
        $country_codes = get_country_code();
        return view('admin.marketing.edit', compact('edit_data', 'teams', 'country_codes'));
    }

    public function update(Request $request, $id)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager())) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'code' => 'nullable|string|max:10',
            'password' => 'nullable|string|min:6',
            'team_id' => 'nullable|exists:teams,id',
            'is_team_lead' => 'nullable|boolean',
            'joining_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        // Verify team is a marketing team
        if ($request->team_id) {
            $team = Team::find($request->team_id);
            if (!$team || !$team->marketing_team) {
                return redirect()->back()->with('message_danger', 'Selected team must be a marketing team.')->withInput();
            }
        }

        $marketing = User::findOrFail($id);
        
        // Filter only the fields we need
        $updateData = $request->only(['name', 'email', 'phone', 'code', 'team_id', 'joining_date']);
        $updateData['is_team_lead'] = $request->has('is_team_lead') ? 1 : 0;

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $marketing->update($updateData);

        // Handle team lead assignment
        if ($request->has('is_team_lead') && $request->team_id) {
            // First, remove any existing team lead from this team
            $existingTeamLead = Team::where('id', $request->team_id)->first();
            if ($existingTeamLead && $existingTeamLead->team_lead_id && $existingTeamLead->team_lead_id != $marketing->id) {
                User::where('id', $existingTeamLead->team_lead_id)->update(['is_team_lead' => 0]);
            }
            
            // Set this user as team lead for the team
            Team::where('id', $request->team_id)->update(['team_lead_id' => $marketing->id]);
        } elseif (!$request->has('is_team_lead')) {
            // If user is no longer team lead, remove them as team lead from any team
            Team::where('team_lead_id', $marketing->id)->update(['team_lead_id' => null]);
        }

        return redirect()->route('admin.marketing.index')->with('message_success', 'Marketing user updated successfully!');
    }

    public function delete($id)
    {
        if (!(RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager())) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $marketing = User::findOrFail($id);
        $marketing->delete();
        return redirect()->route('admin.marketing.index')->with('message_success', 'Marketing user deleted successfully!');
    }

    public function changePassword(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = User::findOrFail($id);
        return view('admin.marketing.change-password', compact('edit_data'));
    }

    public function updatePassword(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()->with('message_danger', $firstError)->withInput();
        }

        $marketing = User::findOrFail($id);
        $marketing->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.marketing.index')->with('message_success', 'Password changed successfully!');
    }

    /**
     * Display D2D SKILL PARK form
     */
    public function d2dForm()
    {
        $currentUser = AuthHelper::getCurrentUser();
        
        // Allow access to: Admin, Super Admin, General Manager, and Marketing users (role_id = 13)
        if (!$currentUser) {
            return redirect()->route('dashboard')->with('message_danger', 'Please login to access this page.');
        }
        
        $isMarketing = $currentUser->role_id == 13;
        $isAdminOrManager = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager();
        
        if (!$isMarketing && !$isAdminOrManager) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        // Get marketing users (role_id = 13) - only if not marketing user
        $marketingUsers = collect();
        if (!$isMarketing) {
            $marketingUsers = User::where('role_id', 13)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }
        
        $country_codes = get_country_code();

        return view('admin.marketing.d2d-form', compact('marketingUsers', 'country_codes', 'isMarketing', 'currentUser'));
    }

    /**
     * Submit D2D SKILL PARK form
     */
    public function d2dSubmit(Request $request)
    {
        $currentUser = AuthHelper::getCurrentUser();
        
        // Allow access to: Admin, Super Admin, General Manager, and Marketing users (role_id = 13)
        if (!$currentUser) {
            return redirect()->route('dashboard')->with('message_danger', 'Please login to access this page.');
        }
        
        $isMarketing = $currentUser->role_id == 13;
        $isAdminOrManager = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager();
        
        if (!$isMarketing && !$isAdminOrManager) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        // Determine marketing_bde_id
        $marketingBdeId = null;
        if ($isMarketing) {
            // If marketing user is logged in, use their ID
            $marketingBdeId = $currentUser->id;
        } else {
            // If admin/manager, require BDE selection
            $validator = Validator::make($request->all(), [
                'bde_id' => 'required|exists:users,id',
            ]);
            
            if ($validator->fails()) {
                return redirect()->back()
                    ->with('message_danger', $validator->errors()->first())
                    ->withInput();
            }
            
            // Verify BDE is a marketing user
            $bde = User::findOrFail($request->bde_id);
            if ($bde->role_id != 13) {
                return redirect()->back()
                    ->with('message_danger', 'Selected BDE must be a marketing user.')
                    ->withInput();
            }
            
            $marketingBdeId = $request->bde_id;
        }

        $validator = Validator::make($request->all(), [
            'date_of_visit' => 'required|date',
            'location' => 'required|string|max:255',
            'house_number' => 'nullable|string|max:255',
            'lead_name' => 'required|string|max:255',
            'code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'whatsapp_code' => 'nullable|string|max:10',
            'whatsapp' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'lead_type' => 'required|string|in:Student,Parent,Working Professional,Institution Representative,Others',
            'interested_courses' => 'nullable|array',
            'interested_courses.*' => 'string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('message_danger', $validator->errors()->first())
                ->withInput();
        }

        // Check for duplicate phone number (code + phone combination)
        // Exclude soft-deleted records to allow re-adding deleted leads
        $duplicatePhone = MarketingLead::where('code', $request->code)
            ->where('phone', $request->phone)
            ->whereNull('deleted_at')
            ->first();

        if ($duplicatePhone) {
            return redirect()->back()
                ->with('message_danger', 'A lead with this phone number ('.$request->code.' '.$request->phone.') already exists in the system. Please check the existing lead or use a different phone number.')
                ->withInput();
        }

        // Check for duplicate submission within last 10 seconds (same data)
        $recentDuplicate = MarketingLead::where('marketing_bde_id', $marketingBdeId)
            ->where('lead_name', $request->lead_name)
            ->where('phone', $request->phone)
            ->where('code', $request->code)
            ->where('date_of_visit', $request->date_of_visit)
            ->where('created_at', '>=', now()->subSeconds(10))
            ->first();

        if ($recentDuplicate) {
            return redirect()->back()
                ->with('message_danger', 'This form was already submitted recently. Please wait a moment before submitting again.')
                ->withInput();
        }

        // Prepare marketing lead data
        $marketingLeadData = [
            'marketing_bde_id' => $marketingBdeId,
            'date_of_visit' => $request->date_of_visit,
            'location' => $request->location,
            'house_number' => $request->house_number,
            'lead_name' => $request->lead_name,
            'code' => $request->code,
            'phone' => $request->phone,
            'whatsapp_code' => $request->whatsapp_code,
            'whatsapp' => $request->whatsapp,
            'address' => $request->address,
            'lead_type' => $request->lead_type,
            'interested_courses' => $request->interested_courses ?? [],
            'remarks' => $request->remarks,
            'is_telecaller_assigned' => false,
            'created_by' => AuthHelper::getCurrentUserId(),
            'updated_by' => AuthHelper::getCurrentUserId(),
        ];

        // Create the marketing lead
        $marketingLead = MarketingLead::create($marketingLeadData);

        if ($marketingLead) {
            return redirect()->route('admin.marketing.d2d-form')
                ->with('message_success', 'D2D form submitted successfully!');
        }

        return redirect()->back()
            ->with('message_danger', 'Something went wrong! Please try again.')
            ->withInput();
    }

    /**
     * Check for duplicate phone number (AJAX)
     */
    public function checkDuplicatePhone(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
        ]);

        // Exclude soft-deleted records to allow re-adding deleted leads
        $duplicate = MarketingLead::where('code', $request->code)
            ->where('phone', $request->phone)
            ->whereNull('deleted_at')
            ->first();

        if ($duplicate) {
            return response()->json([
                'exists' => true,
                'message' => 'A lead with this phone number ('.$request->code.' '.$request->phone.') already exists.',
                'lead' => [
                    'id' => $duplicate->id,
                    'name' => $duplicate->lead_name,
                    'date_of_visit' => $duplicate->date_of_visit->format('Y-m-d'),
                    'location' => $duplicate->location,
                ]
            ]);
        }

        return response()->json([
            'exists' => false,
            'message' => 'Phone number is available.'
        ]);
    }

    /**
     * Display Marketing Leads listing
     */
    public function marketingLeads(Request $request)
    {
        $currentUser = AuthHelper::getCurrentUser();
        
        // Allow access to: Admin, Super Admin, General Manager, and Marketing users (role_id = 13)
        if (!$currentUser) {
            return redirect()->route('dashboard')->with('message_danger', 'Please login to access this page.');
        }
        
        $isMarketing = $currentUser->role_id == 13;
        $isAdminOrManager = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager();
        
        if (!$isMarketing && !$isAdminOrManager) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        // Get marketing leads
        $query = MarketingLead::with(['marketingBde:id,name', 'createdBy:id,name']);
        
        // If marketing user, only show their own leads
        if ($isMarketing) {
            $query->where('marketing_bde_id', $currentUser->id);
        }
        
        // Apply filters if any
        if ($request->filled('date_from')) {
            $query->where('date_of_visit', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date_of_visit', '<=', $request->date_to);
        }
        if ($request->filled('bde_id')) {
            $query->where('marketing_bde_id', $request->bde_id);
        }
        if ($request->filled('is_assigned')) {
            $query->where('is_telecaller_assigned', $request->is_assigned == '1');
        }
        
        // Get marketing users for filter (only if admin/manager)
        $marketingUsers = collect();
        if (!$isMarketing) {
            $marketingUsers = User::where('role_id', 13)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        $isAdminOrManager = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager();
        
        return view('admin.marketing.marketing-leads', compact('marketingUsers', 'isMarketing', 'currentUser', 'isAdminOrManager'));
    }

    /**
     * Show edit form for marketing lead
     */
    public function editMarketingLead($id)
    {
        $currentUser = AuthHelper::getCurrentUser();
        
        if (!$currentUser) {
            return response()->json(['error' => 'Please login to access this page.'], 403);
        }
        
        $isMarketing = $currentUser->role_id == 13;
        $isAdminOrManager = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager();
        
        if (!$isMarketing && !$isAdminOrManager) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $marketingLead = MarketingLead::findOrFail($id);
        
        // If marketing user, only allow editing their own leads
        if ($isMarketing && $marketingLead->marketing_bde_id != $currentUser->id) {
            return response()->json(['error' => 'Access denied. You can only edit your own leads.'], 403);
        }

        $country_codes = get_country_code();
        
        // Get marketing users for BDE selection (only if admin/manager)
        $marketingUsers = collect();
        if (!$isMarketing) {
            $marketingUsers = User::where('role_id', 13)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        return view('admin.marketing.edit-marketing-lead', compact('marketingLead', 'country_codes', 'marketingUsers', 'isMarketing', 'currentUser'));
    }

    /**
     * Update marketing lead
     */
    public function updateMarketingLead(Request $request, $id)
    {
        $currentUser = AuthHelper::getCurrentUser();
        
        if (!$currentUser) {
            return response()->json(['error' => 'Please login to access this page.'], 403);
        }
        
        $isMarketing = $currentUser->role_id == 13;
        $isAdminOrManager = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager();
        
        if (!$isMarketing && !$isAdminOrManager) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $marketingLead = MarketingLead::findOrFail($id);
        
        // If marketing user, only allow editing their own leads
        if ($isMarketing && $marketingLead->marketing_bde_id != $currentUser->id) {
            return response()->json(['error' => 'Access denied. You can only edit your own leads.'], 403);
        }

        // Determine marketing_bde_id
        $marketingBdeId = $marketingLead->marketing_bde_id; // Keep existing by default
        if (!$isMarketing && $request->filled('bde_id')) {
            // If admin/manager, allow changing BDE
            $bde = User::findOrFail($request->bde_id);
            if ($bde->role_id != 13) {
                return response()->json(['error' => 'Selected BDE must be a marketing user.'], 422);
            }
            $marketingBdeId = $request->bde_id;
        }

        $validator = Validator::make($request->all(), [
            'date_of_visit' => 'required|date',
            'location' => 'required|string|max:255',
            'house_number' => 'nullable|string|max:255',
            'lead_name' => 'required|string|max:255',
            'code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'whatsapp_code' => 'nullable|string|max:10',
            'whatsapp' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'lead_type' => 'required|string|in:Student,Parent,Working Professional,Institution Representative,Others',
            'interested_courses' => 'nullable|array',
            'interested_courses.*' => 'string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        // Update marketing lead
        $marketingLead->update([
            'marketing_bde_id' => $marketingBdeId,
            'date_of_visit' => $request->date_of_visit,
            'location' => $request->location,
            'house_number' => $request->house_number,
            'lead_name' => $request->lead_name,
            'code' => $request->code,
            'phone' => $request->phone,
            'whatsapp_code' => $request->whatsapp_code,
            'whatsapp' => $request->whatsapp,
            'address' => $request->address,
            'lead_type' => $request->lead_type,
            'interested_courses' => $request->interested_courses ?? [],
            'remarks' => $request->remarks,
            'updated_by' => AuthHelper::getCurrentUserId(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Marketing lead updated successfully!',
            'redirect' => route('admin.marketing.marketing-leads')
        ]);
    }

    /**
     * AJAX endpoint for DataTables to fetch marketing leads data
     */
    public function getMarketingLeadsData(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            set_time_limit(config('timeout.max_execution_time', 300));

            $currentUser = AuthHelper::getCurrentUser();
            
            if (!$currentUser) {
                return response()->json(['error' => 'Please login to access this page.'], 403);
            }
            
            $isMarketing = $currentUser->role_id == 13;
            $isAdminOrManager = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager();
            
            if (!$isMarketing && !$isAdminOrManager) {
                return response()->json(['error' => 'Access denied.'], 403);
            }

            // Build base query
            $query = MarketingLead::with([
                'marketingBde:id,name', 
                'createdBy:id,name',
                'lead.leadStatus',
                'lead.telecaller:id,name',
                'lead.convertedLead'
            ]);
            
            // If marketing user, only show their own leads
            if ($isMarketing) {
                $query->where('marketing_bde_id', $currentUser->id);
            }
            
            // Get total count before filtering
            $totalRecords = $query->count();
            
            // Apply filters
            if ($request->filled('date_from')) {
                $query->where('date_of_visit', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->where('date_of_visit', '<=', $request->date_to);
            }
            if ($request->filled('bde_id')) {
                $query->where('marketing_bde_id', $request->bde_id);
            }
            if ($request->filled('is_assigned')) {
                $query->where('is_telecaller_assigned', $request->is_assigned == '1');
            }
            if ($request->has('is_converted') && $request->is_converted !== '') {
                if ($request->is_converted === '1') {
                    $query->whereHas('lead', function ($leadQuery) {
                        $leadQuery->where('is_converted', true);
                    });
                } elseif ($request->is_converted === '0') {
                    $query->where(function ($subQuery) {
                        $subQuery->whereDoesntHave('lead')
                            ->orWhereHas('lead', function ($leadQuery) {
                                $leadQuery->where(function ($conversionQuery) {
                                    $conversionQuery->where('is_converted', false)
                                        ->orWhereNull('is_converted');
                                });
                            });
                    });
                }
            }
            
            // Apply DataTables search (from DataTables search box)
            if ($request->filled('search') && is_array($request->search) && isset($request->search['value']) && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where(function($q) use ($searchValue) {
                    $q->where('lead_name', 'LIKE', "%{$searchValue}%")
                      ->orWhere('phone', 'LIKE', "%{$searchValue}%")
                      ->orWhere('whatsapp', 'LIKE', "%{$searchValue}%")
                      ->orWhere('location', 'LIKE', "%{$searchValue}%")
                      ->orWhere('address', 'LIKE', "%{$searchValue}%")
                      ->orWhere('remarks', 'LIKE', "%{$searchValue}%");
                });
            }
            
            // Get filtered count
            $filteredCount = $query->count();
            
            // Column mapping for ordering
            $columns = [
                0 => 'id', // Index
                1 => 'date_of_visit', // Date of Visit
                2 => 'marketing_bde_id', // BDE Name
                3 => 'lead_name', // Lead Name
                4 => 'phone', // Phone
                5 => 'whatsapp', // WhatsApp
                6 => 'address', // Address
                7 => 'location', // Location
                8 => 'house_number', // House Number
                9 => 'lead_type', // Lead Type
                10 => 'id', // Interested Courses (no sorting)
                11 => 'remarks', // Remarks
                12 => 'id', // Telecaller Remarks (no sorting)
                13 => 'id', // Lead Status (no sorting)
                14 => 'id', // Converted (no sorting)
                15 => 'id', // Telecaller Name (no sorting)
                16 => 'is_telecaller_assigned', // Assignment Status
                17 => 'assigned_at', // Assigned At
                18 => 'created_at', // Created At
                19 => 'id', // Actions (no sorting)
            ];
            
            // Apply ordering
            $order = $request->get('order', []);
            $orderColumn = isset($order[0]['column']) ? (int)$order[0]['column'] : 18; // Default to created_at
            $orderDir = isset($order[0]['dir']) ? $order[0]['dir'] : 'desc';
            
            $orderColumnName = $columns[$orderColumn] ?? 'created_at';
            if ($orderColumnName !== 'id') {
                $query->orderBy($orderColumnName, $orderDir);
            } else {
                $query->orderBy('created_at', 'desc');
            }
            
            // Apply pagination
            $start = $request->get('start', 0);
            $length = $request->get('length', 25);
            $marketingLeads = $query->skip($start)->take($length)->get();
            
            // Build data array
            $data = [];
            foreach ($marketingLeads as $index => $lead) {
                $interestedCoursesHtml = '-';
                if ($lead->interested_courses && count($lead->interested_courses) > 0) {
                    $badges = [];
                    foreach ($lead->interested_courses as $course) {
                        $badges[] = '<span class="badge bg-secondary me-1">' . htmlspecialchars($course) . '</span>';
                    }
                    $interestedCoursesHtml = implode('', $badges);
                }
                
                $remarksHtml = '-';
                if ($lead->remarks) {
                    $remarksHtml = '<span class="text-truncate d-inline-block" style="max-width: 200px;" title="' . htmlspecialchars($lead->remarks) . '">' . 
                                   htmlspecialchars(Str::limit($lead->remarks, 50)) . '</span>';
                }
                
                // Get telecaller remarks from leads table
                $telecallerRemarksHtml = '-';
                $relatedLead = $lead->lead;
                if ($relatedLead && !empty($relatedLead->remarks)) {
                    $telecallerRemarksHtml = '<span class="text-truncate d-inline-block" style="max-width: 200px;" title="' . htmlspecialchars($relatedLead->remarks) . '">' . 
                                             htmlspecialchars(Str::limit($relatedLead->remarks, 50)) . '</span>';
                }
                
                // Get lead status from leads table
                $leadStatusHtml = '-';
                if ($relatedLead && $relatedLead->leadStatus) {
                    $leadStatusHtml = '<span class="badge bg-primary">' . htmlspecialchars($relatedLead->leadStatus->title) . '</span>';
                } elseif ($relatedLead && $relatedLead->lead_status_id) {
                    // If relationship didn't load, try to get status directly
                    $leadStatusHtml = '<span class="badge bg-secondary">Status ID: ' . $relatedLead->lead_status_id . '</span>';
                }
                
                // Get telecaller name from leads table
                $telecallerName = '-';
                if ($relatedLead && $relatedLead->telecaller) {
                    $telecallerName = htmlspecialchars($relatedLead->telecaller->name);
                } elseif ($relatedLead && $relatedLead->telecaller_id) {
                    // If relationship didn't load, try to get telecaller directly
                    $telecaller = \App\Models\User::find($relatedLead->telecaller_id);
                    if ($telecaller) {
                        $telecallerName = htmlspecialchars($telecaller->name);
                    }
                }

                $convertedLeadHtml = '<span class="badge bg-secondary">No</span>';
                $hasConvertedRecord = $relatedLead && ($relatedLead->is_converted || $relatedLead->convertedLead);

                if ($hasConvertedRecord) {
                    $convertedLeadHtml = '<span class="badge bg-success">Yes</span>';
                }
                
                $row = [
                    'index' => $start + $index + 1,
                    'date_of_visit' => $lead->date_of_visit ? $lead->date_of_visit->format('M d, Y') : '-',
                    'bde_name' => $lead->marketingBde ? htmlspecialchars($lead->marketingBde->name) : '-',
                    'lead_name' => htmlspecialchars($lead->lead_name),
                    'phone' => ($lead->code ? htmlspecialchars($lead->code) . ' ' : '') . htmlspecialchars($lead->phone),
                    'whatsapp' => $lead->whatsapp ? (($lead->whatsapp_code ? htmlspecialchars($lead->whatsapp_code) . ' ' : '') . htmlspecialchars($lead->whatsapp)) : '-',
                    'address' => $lead->address ? htmlspecialchars($lead->address) : '-',
                    'location' => htmlspecialchars($lead->location),
                    'house_number' => $lead->house_number ? htmlspecialchars($lead->house_number) : '-',
                    'lead_type' => '<span class="badge bg-info">' . htmlspecialchars($lead->lead_type) . '</span>',
                    'interested_courses' => $interestedCoursesHtml,
                    'remarks' => $remarksHtml,
                    'telecaller_remarks' => $telecallerRemarksHtml,
                    'lead_status' => $leadStatusHtml,
                    'converted_lead' => $convertedLeadHtml,
                    'telecaller_name' => $telecallerName,
                    'assignment_status' => $lead->is_telecaller_assigned 
                        ? '<span class="badge bg-success">Assigned</span>' 
                        : '<span class="badge bg-warning">Not Assigned</span>',
                    'assigned_at' => $lead->assigned_at ? $lead->assigned_at->format('M d, Y H:i') : '-',
                    'created_at' => $lead->created_at ? $lead->created_at->format('M d, Y H:i') : '-',
                    'actions' => $this->renderMarketingLeadActions($lead, $isAdminOrManager)
                ];
                
                $data[] = $row;
            }
            
            // Build response array
            $responseData = [
                'draw' => intval($request->get('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredCount,
                'data' => $data
            ];
            
            return response()->json($responseData);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching marketing leads data: ' . $e->getMessage());
            return response()->json([
                'draw' => intval($request->get('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error loading data. Please try again.'
            ], 500);
        }
    }

    /**
     * Assign a single marketing lead to telecaller
     */
    public function assignToTelecaller(Request $request, $id)
    {
        $currentUser = AuthHelper::getCurrentUser();
        
        if (!$currentUser) {
            return response()->json(['error' => 'Please login to access this page.'], 403);
        }
        
        $isAdminOrManager = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager();
        
        if (!$isAdminOrManager) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'telecaller_id' => 'required|exists:users,id',
            'marketing_remarks' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        // Verify telecaller is a telecaller (role_id = 3)
        $telecaller = User::findOrFail($request->telecaller_id);
        if ($telecaller->role_id != 3) {
            return response()->json(['error' => 'Selected user must be a telecaller.'], 422);
        }

        $marketingLead = MarketingLead::findOrFail($id);
        
        // Check if already assigned
        if ($marketingLead->is_telecaller_assigned) {
            return response()->json(['error' => 'This marketing lead is already assigned to a telecaller.'], 422);
        }

        try {
            \DB::beginTransaction();

            // Prepare remarks with course names
            $remarks = $request->marketing_remarks ?? $marketingLead->remarks ?? '';
            
            // Get interested courses and format them
            $interestedCourses = $marketingLead->interested_courses ?? [];
            if (!empty($interestedCourses) && is_array($interestedCourses)) {
                $coursesText = 'Interested Courses: ' . implode(', ', $interestedCourses);
                if (!empty($remarks)) {
                    $remarks = $remarks . "\n\n" . $coursesText;
                } else {
                    $remarks = $coursesText;
                }
            }

            // Prepare marketing remarks - use the original marketing lead remarks or request remarks
            $marketingRemarks = $request->marketing_remarks ?? $marketingLead->remarks ?? '';

            // Create lead from marketing lead
            $lead = Lead::create([
                'marketing_leads_id' => $marketingLead->id,
                'place' => $marketingLead->location,
                'title' => $marketingLead->lead_name,
                'code' => $marketingLead->code,
                'phone' => $marketingLead->phone,
                'whatsapp_code' => $marketingLead->whatsapp_code,
                'whatsapp' => $marketingLead->whatsapp,
                'address' => $marketingLead->address,
                'lead_status_id' => 1,
                'lead_source_id' => 9,
                'remarks' => $remarks, // Set remarks field (includes courses)
                'marketing_remarks' => $marketingRemarks, // Set marketing_remarks field (original marketing remarks only)
                'telecaller_id' => $request->telecaller_id,
                'created_by' => AuthHelper::getCurrentUserId(),
            ]);

            // Create lead activity with marketing remarks and course information
            $activityRemarks = 'Marketing lead has been assigned to telecaller ' . $telecaller->name . '.';
            if (!empty($remarks)) {
                $activityRemarks .= "\n\nMarketing Remarks:\n" . $remarks;
            }
            
            LeadActivity::create([
                'lead_id' => $lead->id,
                'lead_status_id' => 1,
                'activity_type' => 'marketing_lead_assigned',
                'description' => 'Marketing lead assigned to telecaller',
                'remarks' => $activityRemarks,
                'created_by' => AuthHelper::getCurrentUserId(),
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            // Update marketing lead
            $marketingLead->update([
                'is_telecaller_assigned' => true,
                'assigned_at' => now(),
                'assigned_by' => AuthHelper::getCurrentUserId(),
                'assigned_to' => $request->telecaller_id,
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Marketing lead assigned to telecaller successfully!',
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error assigning marketing lead to telecaller: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while assigning the lead. Please try again.'], 500);
        }
    }

    /**
     * Show bulk assign form
     */
    public function ajaxBulkAssign()
    {
        $currentUser = AuthHelper::getCurrentUser();
        
        if (!$currentUser) {
            return response()->json(['error' => 'Please login to access this page.'], 403);
        }
        
        $isAdminOrManager = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager();
        
        if (!$isAdminOrManager) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Get all telecallers (role_id = 3)
        $telecallers = User::where('role_id', 3)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get marketing users (BDEs) for filter
        $marketingUsers = User::where('role_id', 13)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.marketing.ajax-bulk-assign', compact('telecallers', 'marketingUsers'));
    }

    /**
     * Process bulk assign
     */
    public function bulkAssign(Request $request)
    {
        $currentUser = AuthHelper::getCurrentUser();
        
        if (!$currentUser) {
            return redirect()->back()->with('message_danger', 'Please login to access this page.');
        }
        
        $isAdminOrManager = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager();
        
        if (!$isAdminOrManager) {
            return redirect()->back()->with('message_danger', 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'telecaller_id' => 'required|exists:users,id',
            'marketing_lead_id' => 'required|array|min:1',
            'marketing_lead_id.*' => 'exists:marketing_leads,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Verify telecaller is a telecaller (role_id = 3)
        $telecaller = User::findOrFail($request->telecaller_id);
        if ($telecaller->role_id != 3) {
            return redirect()->back()->with('message_danger', 'Selected user must be a telecaller.')->withInput();
        }

        // Get selected marketing leads
        $marketingLeads = MarketingLead::whereIn('id', $request->marketing_lead_id)
            ->where('is_telecaller_assigned', false)
            ->get();

        if ($marketingLeads->isEmpty()) {
            return redirect()->back()->with('message_danger', 'No valid unassigned marketing leads selected.');
        }

        $successCount = 0;
        $errors = [];

        try {
            \DB::beginTransaction();

            foreach ($marketingLeads as $marketingLead) {
                try {
                    // Prepare remarks with course names
                    $remarks = $marketingLead->remarks ?? '';
                    
                    // Get interested courses and format them
                    $interestedCourses = $marketingLead->interested_courses ?? [];
                    if (!empty($interestedCourses) && is_array($interestedCourses)) {
                        $coursesText = '. Interested Courses: ' . implode(', ', $interestedCourses);
                        if (!empty($remarks)) {
                            $remarks = $remarks . "\n\n" . $coursesText;
                        } else {
                            $remarks = $coursesText;
                        }
                    }
                    
                    // Prepare marketing remarks - use the original marketing lead remarks
                    $marketingRemarks = $marketingLead->remarks ?? '';
                    
                    // Create lead from marketing lead
                    $lead = Lead::create([
                        'marketing_leads_id' => $marketingLead->id,
                        'place' => $marketingLead->location,
                        'title' => $marketingLead->lead_name,
                        'code' => $marketingLead->code,
                        'phone' => $marketingLead->phone,
                        'whatsapp_code' => $marketingLead->whatsapp_code,
                        'whatsapp' => $marketingLead->whatsapp,
                        'address' => $marketingLead->address,
                        'lead_status_id' => 1,
                        'lead_source_id' => 9,
                        'remarks' => $remarks, // Set remarks field (includes courses)
                        'marketing_remarks' => $marketingRemarks, // Set marketing_remarks field (original marketing remarks only)
                        'telecaller_id' => $request->telecaller_id,
                        'created_by' => AuthHelper::getCurrentUserId(),
                    ]);

                    // Create lead activity with marketing remarks and course information
                    $activityRemarks = 'Marketing lead has been assigned to telecaller ' . $telecaller->name . ' via bulk assignment.';
                    if (!empty($remarks)) {
                        $activityRemarks .= "\n\nMarketing Remarks:\n" . $remarks;
                    }
                    
                    LeadActivity::create([
                        'lead_id' => $lead->id,
                        'lead_status_id' => 1,
                        'activity_type' => 'marketing_lead_assigned',
                        'description' => 'Marketing lead assigned to telecaller via bulk operation',
                        'remarks' => $activityRemarks,
                        'created_by' => AuthHelper::getCurrentUserId(),
                        'updated_by' => AuthHelper::getCurrentUserId(),
                    ]);

                    // Update marketing lead
                    $marketingLead->update([
                        'is_telecaller_assigned' => true,
                        'assigned_at' => now(),
                        'assigned_by' => AuthHelper::getCurrentUserId(),
                        'assigned_to' => $request->telecaller_id,
                        'updated_by' => AuthHelper::getCurrentUserId(),
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = 'Error assigning lead ' . $marketingLead->lead_name . ': ' . $e->getMessage();
                    \Log::error('Error assigning marketing lead ' . $marketingLead->id . ': ' . $e->getMessage());
                }
            }

            \DB::commit();

            $message = "Successfully assigned {$successCount} marketing lead(s) to telecaller!";
            if (!empty($errors)) {
                $message .= ' Some errors occurred: ' . implode(', ', array_slice($errors, 0, 5));
            }

            return redirect()->back()->with('message_success', $message);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error in bulk assign: ' . $e->getMessage());
            return redirect()->back()->with('message_danger', 'An error occurred during bulk assignment. Please try again.');
        }
    }

    /**
     * Get marketing leads by filters for bulk assign
     */
    public function getMarketingLeadsByFiltersAssign(Request $request)
    {
        $currentUser = AuthHelper::getCurrentUser();
        
        if (!$currentUser) {
            return response()->json(['error' => 'Please login to access this page.'], 403);
        }
        
        $isAdminOrManager = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager();
        
        if (!$isAdminOrManager) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Build query for unassigned marketing leads
        $query = MarketingLead::where('is_telecaller_assigned', false)
            ->whereBetween('date_of_visit', [$request->date_from, $request->date_to]);

        $bdeId = $request->bde_id;
        if ($bdeId === 'all') {
            $bdeId = null;
        }

        // Filter by BDE if provided
        if (!empty($bdeId)) {
            $query->where('marketing_bde_id', $bdeId);
        }

        $marketingLeads = $query->orderBy('date_of_visit', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.marketing.partials.marketing-leads-table-rows-assign', compact('marketingLeads'));
    }

    /**
     * Show assign form for single marketing lead
     */
    public function ajaxAssign($id)
    {
        $currentUser = AuthHelper::getCurrentUser();
        
        if (!$currentUser) {
            return response()->json(['error' => 'Please login to access this page.'], 403);
        }
        
        $isAdminOrManager = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager();
        
        if (!$isAdminOrManager) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $marketingLead = MarketingLead::findOrFail($id);
        
        // Check if already assigned
        if ($marketingLead->is_telecaller_assigned) {
            return response()->json(['error' => 'This marketing lead is already assigned to a telecaller.'], 422);
        }

        // Get all telecallers (role_id = 3)
        $telecallers = User::where('role_id', 3)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.marketing.ajax-assign', compact('telecallers', 'marketingLead'))->with('marketingLeadId', $id);
    }

    /**
     * Render actions column HTML for marketing leads
     */
    private function renderMarketingLeadActions($lead, $isAdminOrManager)
    {
        $html = '<div class="btn-group" role="group">';
        
        // View button (available to all users)
        $html .= '<button type="button" class="btn btn-info btn-sm" ' .
                'onclick="show_large_modal(\'' . route('admin.marketing.marketing-leads.view', $lead->id) . '\', \'Marketing Lead Details\')" ' .
                'title="View Details"><i class="ti ti-eye"></i></button>';
        
        // Only show edit/assign actions for admin or general manager
        if ($isAdminOrManager) {
            // Edit button
            $html .= '<button type="button" class="btn btn-warning btn-sm" ' .
                    'onclick="show_ajax_modal(\'' . route('admin.marketing.marketing-leads.edit', $lead->id) . '\', \'Edit Marketing Lead\')" ' .
                    'title="Edit"><i class="ti ti-edit"></i></button>';
            
            // Assign button (only if not assigned and user has permission)
            if (!$lead->is_telecaller_assigned) {
                $html .= '<button type="button" class="btn btn-success btn-sm" ' .
                        'onclick="show_ajax_modal(\'' . route('admin.marketing.assign-to-telecaller.ajax', $lead->id) . '\', \'Assign to Telecaller\')" ' .
                        'title="Assign to Telecaller"><i class="ti ti-user-plus"></i></button>';
            }
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Show marketing lead details with history
     */
    public function viewMarketingLead($id)
    {
        $currentUser = AuthHelper::getCurrentUser();
        
        if (!$currentUser) {
            return response()->json(['error' => 'Please login to access this page.'], 403);
        }
        
        $isMarketing = $currentUser->role_id == 13;
        $isAdminOrManager = RoleHelper::is_admin_or_super_admin() || RoleHelper::is_general_manager();
        
        if (!$isMarketing && !$isAdminOrManager) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $marketingLead = MarketingLead::with([
            'marketingBde:id,name',
            'createdBy:id,name',
            'lead.leadStatus',
            'lead.telecaller:id,name',
            'lead.leadActivities' => function($query) {
                $query->select('id', 'lead_id', 'reason', 'created_at', 'activity_type', 'description', 'remarks', 'rating', 'followup_date', 'created_by', 'lead_status_id')
                      ->with('createdBy:id,name')
                      ->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);
        
        // If marketing user, only allow viewing their own leads
        if ($isMarketing && $marketingLead->marketing_bde_id != $currentUser->id) {
            return response()->json(['error' => 'Access denied. You can only view your own leads.'], 403);
        }

        $relatedLead = $marketingLead->lead;
        $activities = collect();
        
        if ($relatedLead) {
            $activities = $relatedLead->leadActivities ?? collect();
        }

        return view('admin.marketing.view-marketing-lead', compact('marketingLead', 'relatedLead', 'activities'));
    }
}

