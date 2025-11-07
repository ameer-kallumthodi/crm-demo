<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRole;
use App\Models\Team;
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
}

