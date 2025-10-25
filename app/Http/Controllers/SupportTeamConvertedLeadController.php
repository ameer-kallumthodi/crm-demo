<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConvertedLead;
use App\Models\Subject;
use App\Models\Batch;
use App\Models\AdmissionBatch;
use App\Models\Course;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SupportTeamConvertedLeadController extends Controller
{
    /**
     * Display a listing of converted leads for support team by course
     */
    public function index(Request $request, $courseId = null)
    {
        // If no course ID provided, default to BOSSE (course_id = 2)
        if (!$courseId) {
            $courseId = 2;
        }

        $query = ConvertedLead::with([
            'lead',
            'course',
            'academicAssistant',
            'createdBy',
            'studentDetails',
            'subject',
            'batch',
            'admissionBatch'
        ])->where('course_id', $courseId);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereIn('created_by', $teamMemberIds);
                } else {
                    $query->where('created_by', AuthHelper::getCurrentUserId());
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                $query->where('academic_assistant_id', AuthHelper::getCurrentUserId());
            } elseif (RoleHelper::is_telecaller()) {
                $query->where('created_by', AuthHelper::getCurrentUserId());
            } elseif (RoleHelper::is_support_team()) {
                // Support team can see leads assigned to them or their team if applicable
                // For now, assuming they see all leads for their course, similar to admission counsellor
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhereHas('studentDetails', function ($sq) use ($search) {
                      $sq->where('application_number', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('admission_batch_id')) {
            $query->where('admission_batch_id', $request->admission_batch_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $convertedLeads = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get filter data
        $batches = Batch::orderBy('title')->get();
        $subjects = Subject::where('course_id', $courseId)->orderBy('title')->get();
        $country_codes = \App\Helpers\CountriesHelper::get_country_code();
        $course = Course::find($courseId);

        return view('admin.converted-leads.support-team-index', compact(
            'convertedLeads',
            'batches',
            'subjects',
            'country_codes',
            'course',
            'courseId'
        ));
    }

    /**
     * Update support team details inline
     */
    public function updateSupportTeamDetails(Request $request, $id)
    {
        try {
            $convertedLead = ConvertedLead::findOrFail($id);
            $field = $request->field;
            $value = $request->value;

            // Validate the field and value
            $validationRules = $this->getValidationRules($field);
            if ($validationRules) {
                $validator = Validator::make([$field => $value], [$field => $validationRules]);
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'error' => $validator->errors()->first($field)
                    ], 422);
                }
            }

            // Handle all fields - update in converted_leads table for now
            $convertedLead->$field = $value;
            $convertedLead->save();

            // Format the response value
            $responseValue = $this->formatResponseValue($field, $value);

            return response()->json([
                'success' => true,
                'message' => 'Support Team detail updated successfully.',
                'value' => $responseValue
            ]);
        } catch (\Exception $e) {
            Log::error("Error updating support team detail: " . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get validation rules for inline update fields.
     */
    private function getValidationRules(string $field): ?string
    {
        switch ($field) {
            case 'subject_id':
                return 'nullable|exists:subjects,id';
            case 'registration_status':
                return 'nullable|in:Paid,Not Paid';
            case 'technology_side':
                return 'nullable|in:No Knowledge,Limited Knowledge,Moderate Knowledge,High Knowledge';
            case 'student_status':
                return 'nullable|in:Low Level,Below Medium,Medium Level,Advanced Level';
            case 'call_1':
            case 'call_2':
            case 'call_3':
            case 'call_4':
            case 'call_5':
            case 'call_6':
            case 'call_7':
            case 'call_8':
            case 'call_9':
                return 'nullable|in:Call Not Answered,Switched Off,Line Busy,Student Asks to Call Later,Lack of Interest in Conversation,Wrong Contact,Inconsistent Responses,Task Complete';
            case 'app':
                return 'nullable|in:Provided app,OTP Problem,Task Completed';
            case 'whatsapp_group':
                return 'nullable|in:Sent link,Task Completed';
            case 'telegram_group':
                return 'nullable|in:Call not answered,switched off,line busy,student asks to call later,lack of interest in conversation,wrong contact,inconsistent responses,task complete';
            case 'problems':
                return 'nullable|string|max:500';
            case 'mentor_live_1':
            case 'mentor_live_2':
            case 'mentor_live_3':
            case 'mentor_live_4':
            case 'mentor_live_5':
                return 'nullable|in:Not Respond,Task Complete';
            case 'first_live':
            case 'second_live':
            case 'model_exam_live':
                return 'nullable|in:Not Respond,1 subject attend,2 subject attend,3 subject attend,4 subject attend,5 subject attend,6 subject attend,Task complete';
            case 'first_exam_registration':
            case 'practical':
            case 'self_registration':
            case 'mock_test':
            case 'admit_card':
                return 'nullable|in:Did not,Task complete';
            case 'first_exam':
            case 'second_exam':
            case 'model_exam':
            case 'assignment':
                return 'nullable|in:not respond,1 subject attend,2 subject attend,3 subject attend,4 subject attend,5 subject attend,6 subject attend,task complete';
            case 'exam_subject_1':
            case 'exam_subject_2':
            case 'exam_subject_3':
            case 'exam_subject_4':
            case 'exam_subject_5':
            case 'exam_subject_6':
                return 'nullable|in:Did not log in on time,missed the exam,technical issue,task complete';
            default:
                return 'nullable|string|max:255';
        }
    }

    /**
     * Format the response value for display.
     */
    private function formatResponseValue(string $field, $value): string
    {
        if ($field === 'subject_id' && $value) {
            $subject = Subject::find($value);
            return $subject ? $subject->title : '-';
        }
        return $value ?? '-';
    }
}