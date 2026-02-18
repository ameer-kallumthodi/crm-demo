<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\TeamDetail;
use App\Models\AcademicDeliveryStructure;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class TeamRegistrationController extends Controller
{
    public function showForm($teamId)
    {
        $team = Team::findOrFail($teamId);
        
        // Ensure it is a B2B team
        if (!$team->is_b2b) {
            abort(404);
        }

        // Get Active Courses
        $courses = Course::where('is_active', 1)->get();

        return view('public.team-registration.form', compact('team', 'courses'));
    }

    public function store(Request $request, $teamId)
    {
        $team = Team::findOrFail($teamId);
        
         if (!$team->is_b2b) {
            abort(404, 'Not a B2B Team');
        }

        // Validation
        $validated = $request->validate([
            'legal_name' => 'required|string|max:255',
            'institution_category' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255',
            
            'building_name' => 'required|string|max:255',
            'street_name' => 'required|string|max:255',
            'locality_name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'pin_code' => 'required|string|max:20',
            'district' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',

            'comm_officer_name' => 'required|string|max:255',
            'comm_officer_mobile' => 'required|string|max:20',
            'comm_officer_alt_mobile' => 'nullable|string|max:20',
            'comm_officer_whatsapp' => 'required|string|max:20',
            'comm_officer_email' => 'required|email|max:255',

            'auth_person_name' => 'required|string|max:255',
            'auth_person_designation' => 'required|string|max:255',
            'auth_person_mobile' => 'required|string|max:20',
            'auth_person_email' => 'required|email|max:255',

            'courses' => 'required|array',
            'courses.*' => 'exists:courses,id',
        ]);

        // Transform input if needed to match JSON structure
        $data = $request->except(['_token', 'courses', 'interested_courses_details']);

        // Store selected courses
        $data['interested_courses_details'] = $request->input('courses');


        TeamDetail::updateOrCreate(
            ['team_id' => $team->id],
            $data
        );

        return redirect()->route('public.team.register.success', ['teamId' => $team->id]);
    }

    public function showSuccess($teamId)
    {
        $team = Team::findOrFail($teamId);
        return view('public.team-registration.success', compact('team'));
    }
}
