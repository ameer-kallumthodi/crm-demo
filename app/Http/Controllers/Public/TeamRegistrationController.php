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

        // Get Academic Delivery Structures Grouped by Course
        // Only show courses that have active academic delivery structures
        $courses = Course::with(['academicDeliveryStructures' => function($query) {
            $query->where('status', 1);
        }])->whereHas('academicDeliveryStructures', function($query) {
             $query->where('status', 1);
        })->get();

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

            'interested_courses_details' => 'nullable|array',
        ]);

        // Transform input if needed to match JSON structure
        $data = $request->except(['_token', 'courses', 'delivery_structures']);

        // Since we changed the UI to dynamic rows, we need to process 'delivery_structures' 
        // which now comes as 'interested_courses_details' array of selected values.
        // However, the blade template now sends `interested_courses_details[]` which are just the delivery structure IDs.
        // We might want to store it in a structure that maps course -> structures. 
        // But simply storing the array of structure IDs is also fine if that's what's intended.
        // Let's refine the input processing to map it back to a structure if needed, or just save the array.
        
        // If the request comes from the new dynamic form, `interested_courses_details` might be a flat array of structure IDs.
        // We should Group them by Course ID for better structure if possible, but the current UI sends just structure IDs.
        // To keep it structured:
        
        $interestedCourses = [];
        if ($request->has('courses') && $request->has('interested_courses_details')) {
            $courses = $request->input('courses');
            $deliveryStructures = $request->input('interested_courses_details');
            
            foreach ($courses as $index => $courseId) {
                if (isset($deliveryStructures[$index]) && $courseId) {
                    $structureId = $deliveryStructures[$index];
                    if (!isset($interestedCourses[$courseId])) {
                        $interestedCourses[$courseId] = [];
                    }
                    $interestedCourses[$courseId][] = $structureId;
                }
            }
             $data['interested_courses_details'] = $interestedCourses;
        }


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
