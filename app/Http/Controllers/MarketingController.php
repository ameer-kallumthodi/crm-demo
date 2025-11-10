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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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

        // Get marketing users (role_id = 13)
        $marketingUsers = User::where('role_id', 13)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $country_codes = get_country_code();

        return view('admin.marketing.d2d-form', compact('marketingUsers', 'country_codes'));
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

        $validator = Validator::make($request->all(), [
            'bde_id' => 'required|exists:users,id',
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

        // Verify BDE is a marketing user
        $bde = User::findOrFail($request->bde_id);
        if ($bde->role_id != 13) {
            return redirect()->back()
                ->with('message_danger', 'Selected BDE must be a marketing user.')
                ->withInput();
        }

        // Get or create D2D lead source
        $leadSource = LeadSource::where('title', 'LIKE', '%Door-to-Door%')
            ->orWhere('title', 'LIKE', '%D2D%')
            ->first();
        
        if (!$leadSource) {
            // Try to get any active lead source as fallback
            $leadSource = LeadSource::where('is_active', true)->first();
            if (!$leadSource) {
                return redirect()->back()
                    ->with('message_danger', 'No active lead source found. Please contact administrator.')
                    ->withInput();
            }
        }

        // Get default lead status (usually id 1 for new)
        $leadStatus = LeadStatus::where('is_active', true)->first();
        if (!$leadStatus) {
            return redirect()->back()
                ->with('message_danger', 'No active lead status found. Please contact administrator.')
                ->withInput();
        }

        // Prepare remarks with additional information
        $remarks = $request->remarks ?? '';
        $additionalInfo = [];
        
        if ($request->filled('location')) {
            $additionalInfo[] = "Location: " . $request->location;
        }
        if ($request->filled('house_number')) {
            $additionalInfo[] = "House Number: " . $request->house_number;
        }
        if ($request->filled('date_of_visit')) {
            $additionalInfo[] = "Date of Visit: " . $request->date_of_visit;
        }
        if ($request->filled('lead_type')) {
            $additionalInfo[] = "Lead Type: " . $request->lead_type;
        }
        if (!empty($request->interested_courses)) {
            $additionalInfo[] = "Interested Courses: " . implode(', ', $request->interested_courses);
        }
        
        if (!empty($additionalInfo)) {
            $remarks = (!empty($remarks) ? $remarks . "\n\n" : '') . "D2D Campaign Details:\n" . implode("\n", $additionalInfo);
        }

        // Try to get a default course if interested courses are selected
        // This is optional since D2D leads can have multiple courses
        $courseId = null;
        if (!empty($request->interested_courses)) {
            // Try to match first interested course with database courses
            $firstCourse = $request->interested_courses[0];
            $course = Course::where('title', 'LIKE', '%' . $firstCourse . '%')
                ->where('is_active', true)
                ->first();
            if ($course) {
                $courseId = $course->id;
            }
        }

        // Create lead data
        $leadData = [
            'title' => $request->lead_name,
            'code' => $request->code,
            'phone' => $request->phone,
            'whatsapp_code' => $request->whatsapp_code,
            'whatsapp' => $request->whatsapp,
            'address' => $request->address,
            'lead_status_id' => $leadStatus->id,
            'lead_source_id' => $leadSource->id,
            'course_id' => $courseId, // Can be null for D2D leads with multiple courses
            'interest_status' => $leadStatus->interest_status,
            'remarks' => $remarks,
            'add_date' => $request->date_of_visit,
            'add_time' => date('H:i'),
            'created_by' => AuthHelper::getCurrentUserId(),
            'updated_by' => AuthHelper::getCurrentUserId(),
        ];

        // Set BDE's team if available
        if ($bde->team_id) {
            $leadData['team_id'] = $bde->team_id;
        }

        // Create the lead
        $lead = Lead::create($leadData);

        if ($lead) {
            // Create lead activity
            LeadActivity::create([
                'lead_id' => $lead->id,
                'lead_status_id' => $leadStatus->id,
                'remarks' => $remarks,
                'created_by' => AuthHelper::getCurrentUserId(),
                'updated_by' => AuthHelper::getCurrentUserId()
            ]);

            return redirect()->route('admin.marketing.d2d-form')
                ->with('message_success', 'D2D lead created successfully!');
        }

        return redirect()->back()
            ->with('message_danger', 'Something went wrong! Please try again.')
            ->withInput();
    }
}

