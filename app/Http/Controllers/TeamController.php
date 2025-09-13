<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;
use App\Helpers\AuthHelper;

class TeamController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $teams = Team::with(['teamLead', 'users'])->get();
        $teamLeads = User::where('is_team_lead', true)->get();
        
        return view('admin.teams.index', compact('teams', 'teamLeads'));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $team = Team::create([
            'name' => $request->name,
            'description' => $request->description,
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
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($team->load('teamLead', 'users'));
    }


    public function destroy(Team $team)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
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
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        return view('admin.teams.add');
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $team = Team::create([
            'name' => $request->name,
            'description' => $request->description,
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
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = Team::findOrFail($id);
        return view('admin.teams.edit', compact('edit_data'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $team = Team::findOrFail($id);
        $team->update([
            'name' => $request->name,
            'description' => $request->description,
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
        if (!RoleHelper::is_admin_or_super_admin()) {
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
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $team = Team::with(['teamLead', 'users' => function($query) {
            $query->where('role_id', 3); // Only telecallers
        }])->findOrFail($id);

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

        // Get available telecallers (not assigned to any team)
        $availableTelecallers = User::where('role_id', 3)
            ->whereNull('team_id')
            ->whereNull('deleted_at')
            ->get();

        return view('admin.teams.members', compact('team', 'allTeamMembers', 'availableTelecallers'));
    }

    /**
     * Remove team member
     */
    public function removeMember(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
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
        if (!RoleHelper::is_admin_or_super_admin()) {
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
}