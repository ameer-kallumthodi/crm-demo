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

        // Get Active Courses with their active Academic Delivery Structures
        $courses = Course::where('is_active', 1)
            ->with(['academicDeliveryStructures' => function($query) {
                $query->where('status', 1);
            }])
            ->get();

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
            'telephone' => 'nullable|string|max:255',
            
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

            'items' => 'nullable|array',
            'items.*.course_id' => 'required',
            'items.*.structures' => 'nullable|array',
        ]);

        // Transform input if needed to match JSON structure
        $data = $request->except(['_token', 'items', 'courses', 'interested_courses_details']);

        $interestedCourses = [];
        if ($request->has('items')) {
            foreach ($request->items as $item) {
                if (!empty($item['course_id']) && !empty($item['structures'])) {
                    $courseId = $item['course_id'];
                    $structures = $item['structures'];
                    
                    if (!isset($interestedCourses[$courseId])) {
                        $interestedCourses[$courseId] = [];
                    }
                    $interestedCourses[$courseId] = array_values(array_unique(array_merge($interestedCourses[$courseId], $structures)));
                }
            }
        }
         $data['interested_courses_details'] = $interestedCourses;


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
