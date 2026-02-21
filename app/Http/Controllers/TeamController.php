<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamDetail;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;
use App\Helpers\AuthHelper;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class TeamController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $teams = Team::with(['teamLead', 'users', 'detail'])->get();
        $teamLeads = User::where('is_team_lead', true)->get();

        return view('admin.teams.index', compact('teams', 'teamLeads'));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'marketing_team' => 'nullable|boolean',
            'is_b2b' => 'nullable|boolean',
        ]);

        $team = Team::create([
            'name' => $request->name,
            'description' => $request->description,
            'marketing_team' => $request->has('marketing_team') ? (bool)$request->marketing_team : false,
            'is_b2b' => $request->has('is_b2b') ? (bool)$request->is_b2b : false,
            'created_by' => AuthHelper::getCurrentUserId(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Team created successfully.',
            'data' => $team->load('teamLead')
        ]);
    }

    public function show(Team $team)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($team->load('teamLead', 'users'));
    }


    public function destroy(Team $team)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Check if team has users
        if ($team->users()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete team. It has assigned users.'
            ], 422);
        }

        $team->delete();

        return response()->json([
            'success' => true,
            'message' => 'Team deleted successfully.'
        ]);
    }

    public function ajax_add()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        return view('admin.teams.add');
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'marketing_team' => 'nullable|boolean',
            'is_b2b' => 'nullable|boolean',
        ]);

        $team = Team::create([
            'name' => $request->name,
            'description' => $request->description,
            'marketing_team' => $request->has('marketing_team') ? (bool)$request->marketing_team : false,
            'is_b2b' => $request->has('is_b2b') ? (bool)$request->is_b2b : false,
            'created_by' => AuthHelper::getCurrentUserId(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Team created successfully!',
                'data' => $team
            ]);
        }

        return redirect()->route('admin.teams.index')->with('message_success', 'Team created successfully!');
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = Team::findOrFail($id);
        return view('admin.teams.edit', compact('edit_data'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'marketing_team' => 'nullable|boolean',
            'is_b2b' => 'nullable|boolean',
        ]);

        $team = Team::findOrFail($id);
        $team->update([
            'name' => $request->name,
            'description' => $request->description,
            'marketing_team' => $request->has('marketing_team') ? (bool)$request->marketing_team : false,
            'is_b2b' => $request->has('is_b2b') ? (bool)$request->is_b2b : false,
            'updated_by' => AuthHelper::getCurrentUserId(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Team updated successfully!',
                'data' => $team
            ]);
        }

        return redirect()->route('admin.teams.index')->with('message_success', 'Team updated successfully!');
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $team = Team::findOrFail($id);

        // Check if team has users
        if ($team->users()->count() > 0) {
            return redirect()->route('admin.teams.index')->with('message_error', 'Cannot delete team. It has assigned users.');
        }

        $team->delete();
        return redirect()->route('admin.teams.index')->with('message_success', 'Team deleted successfully!');
    }

    /**
     * Show team members in AJAX modal
     */
    public function members($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $team = Team::with('teamLead')->findOrFail($id);

        // Load users based on team type
        if ($team->marketing_team) {
            $team->load(['users' => function ($query) {
                $query->where('role_id', 13); // Marketing users
            }]);
        }
        else {
            $team->load(['users' => function ($query) {
                $query->where('role_id', 3); // Telecallers
            }]);
        }

        // Get all team members including the team lead
        $allTeamMembers = collect();

        // Add team lead if exists
        if ($team->teamLead) {
            $allTeamMembers->push($team->teamLead);
        }

        // Add regular team members
        $allTeamMembers = $allTeamMembers->merge($team->users);

        // Remove duplicates (in case team lead is also in users)
        $allTeamMembers = $allTeamMembers->unique('id');

        // Get available users based on team type
        if ($team->marketing_team) {
            // For marketing teams, show marketing users (role_id 13)
            $availableUsers = User::where('role_id', 13)
                ->whereNull('team_id')
                ->whereNull('deleted_at')
                ->get();
        }
        else {
            // For sales teams, show telecallers (role_id 3)
            $availableUsers = User::where('role_id', 3)
                ->whereNull('team_id')
                ->whereNull('deleted_at')
                ->get();
        }

        return view('admin.teams.members', compact('team', 'allTeamMembers', 'availableUsers'));
    }

    /**
     * Remove team member
     */
    public function removeMember(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->update(['team_id' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Team member removed successfully!'
        ]);
    }

    /**
     * Add team member
     */
    public function addMember(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'team_id' => 'required|exists:teams,id',
        ]);

        $user = User::findOrFail($request->user_id);

        // Check if user is already assigned to a team
        if ($user->team_id) {
            return response()->json([
                'success' => false,
                'message' => 'User is already assigned to a team!'
            ]);
        }

        $user->update(['team_id' => $request->team_id]);

        return response()->json([
            'success' => true,
            'message' => 'Team member added successfully!'
        ]);
    }
    /**
     * Show team registration details
     */
    public function showDetails($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $team = Team::with('detail')->findOrFail($id);

        // Ensure it is a B2B team or at least has details
        if (!$team->is_b2b || !$team->detail) {
            return redirect()->back()->with('message_danger', 'No registration details found for this team.');
        }

        $detail = $team->detail;

        $interestedCourses = [];
        if ($detail->interested_courses_details && is_array($detail->interested_courses_details)) {
            $courseIds = array_keys($detail->interested_courses_details);
            $courses = \App\Models\Course::whereIn('id', $courseIds)->get()->keyBy('id');

            // Collect all structure IDs
            $allStructureIds = [];
            foreach ($detail->interested_courses_details as $cId => $sIds) {
                if (is_array($sIds)) {
                    $allStructureIds = array_merge($allStructureIds, $sIds);
                }
            }

            $structures = \App\Models\AcademicDeliveryStructure::whereIn('id', $allStructureIds)->get()->keyBy('id');

            foreach ($detail->interested_courses_details as $cId => $sIds) {
                if (isset($courses[$cId])) {
                    $courseName = $courses[$cId]->title;
                    $structureNames = [];
                    if (is_array($sIds)) {
                        foreach ($sIds as $sId) {
                            if (isset($structures[$sId])) {
                                $structureNames[] = $structures[$sId]->title;
                            }
                        }
                    }
                    $interestedCourses[] = [
                        'course' => $courseName,
                        'structures' => $structureNames
                    ];
                }

            }
        }



        $allCourses = \App\Models\Course::with(['academicDeliveryStructures' => function ($q) {
            $q->where('status', 1);
        }])->where('is_active', 1)->get();

        return view('admin.teams.details', compact('team', 'detail', 'interestedCourses', 'allCourses'));
    }
    public function updateDetails(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $team = Team::with('detail')->findOrFail($id);

        if (!$team->detail) {
            return response()->json(['error' => 'Registration details not found!'], 404);
        }

        $request->validate([
            'field' => 'required|string',
            'value' => 'nullable',
        ]);

        $field = $request->field;
        $value = $request->value;

        // Allowed fields to be updated
        $allowedFields = [
            'legal_name', 'institution_category', 'telephone',
            'building_name', 'street_name', 'locality_name', 'city', 'district', 'state', 'pin_code', 'country',
            'comm_officer_name', 'comm_officer_mobile', 'comm_officer_alt_mobile', 'comm_officer_whatsapp', 'comm_officer_email',
            'auth_person_name', 'auth_person_designation', 'auth_person_mobile', 'auth_person_email',
            'interested_courses_details',
            'b2b_partner_id', 'b2b_code', 'date_of_joining', 'partner_status',
            'b2b_officer_name', 'employee_id', 'designation', 'official_contact_number', 'whatsapp_business_number', 'official_email_id',
            'working_days', 'office_hours', 'break_time', 'holiday_policy',
            'account_holder_name', 'bank_name', 'account_number', 'ifsc_code',
            'terms_and_conditions'
        ];

        if (!in_array($field, $allowedFields)) {
            return response()->json(['error' => 'Invalid field.'], 400);
        }

        $team->detail->update([
            $field => $value
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully!',
            'value' => $value
        ]);
    }

    public function exportDetailsPdf($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            abort(403);
        }

        $team = Team::with('detail')->findOrFail($id);

        if (!$team->is_b2b || !$team->detail) {
            abort(404);
        }

        $detail = $team->detail;

        $interestedCourses = [];
        if ($detail->interested_courses_details && is_array($detail->interested_courses_details)) {
            $courseIds = array_keys($detail->interested_courses_details);
            $courses = \App\Models\Course::whereIn('id', $courseIds)->get()->keyBy('id');

            // Collect all structure IDs
            $allStructureIds = [];
            foreach ($detail->interested_courses_details as $cId => $sIds) {
                if (is_array($sIds)) {
                    $allStructureIds = array_merge($allStructureIds, $sIds);
                }
            }

            $structures = \App\Models\AcademicDeliveryStructure::whereIn('id', $allStructureIds)->get()->keyBy('id');

            foreach ($detail->interested_courses_details as $cId => $sIds) {
                if (isset($courses[$cId])) {
                    $courseName = $courses[$cId]->title;
                    $structureNames = [];
                    if (is_array($sIds)) {
                        foreach ($sIds as $sId) {
                            if (isset($structures[$sId])) {
                                $structureNames[] = $structures[$sId]->title;
                            }
                        }
                    }
                    $interestedCourses[] = [
                        'course' => $courseName,
                        'structures' => $structureNames
                    ];
                }

            }
        }

        $pdf = \PDF::loadView('admin.teams.details_pdf', compact('team', 'detail', 'interestedCourses'));
        return $pdf->download('team_details_' . $team->id . '.pdf');
    }

    public function termsAndConditions($id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $team = Team::with('detail')->findOrFail($id);

        if (!$team->is_b2b) {
            abort(404);
        }

        $termsAndConditions = $team->detail?->terms_and_conditions ?? '';

        return view('admin.teams.terms-and-conditions', compact('team', 'termsAndConditions'));
    }

    public function updateTermsAndConditions(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $team = Team::with('detail')->findOrFail($id);

        if (!$team->is_b2b) {
            return response()->json(['error' => 'Invalid team.'], 404);
        }

        $request->validate([
            'terms_and_conditions' => 'nullable|string',
        ]);

        $detail = $team->detail;

        if (!$detail) {
            $detail = TeamDetail::create([
                'team_id' => $team->id,
                'terms_and_conditions' => $request->terms_and_conditions,
            ]);
        } else {
            $detail->update([
                'terms_and_conditions' => $request->terms_and_conditions,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Terms and conditions updated successfully.',
        ]);
    }
}