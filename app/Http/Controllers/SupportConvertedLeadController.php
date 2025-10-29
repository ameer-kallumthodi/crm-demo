<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConvertedLead;
use App\Models\ConvertedStudentMentorDetail;
use App\Models\ConvertedStudentSupportDetail;
use App\Models\SupportFeedbackHistory;
use App\Models\Subject;
use App\Models\Batch;
use App\Models\AdmissionBatch;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SupportConvertedLeadController extends Controller
{
    /**
     * Display a listing of BOSSE converted leads for support
     */
    public function index(Request $request)
    {
        $query = ConvertedLead::with([
            'lead', 
            'course', 
            'academicAssistant', 
            'createdBy', 
            'studentDetails',
            'supportDetails',
            'subject',
            'batch',
            'admissionBatch'
        ])->where('course_id', 2); // BOSSE course

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                        $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                    });
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Can see all
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('studentDetails', function($subQ) use ($search) {
                      $subQ->where('application_number', 'like', "%{$search}%");
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

        if ($request->filled('registration_status')) {
            $query->whereHas('supportDetails', function($q) use ($request) {
                $q->where('registration_status', $request->registration_status);
            });
        }

        if ($request->filled('student_status')) {
            $query->whereHas('supportDetails', function($q) use ($request) {
                $q->where('student_status', $request->student_status);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $convertedLeads = $query->orderBy('created_at', 'desc')->paginate(50);
        $batches = Batch::where('course_id', 2)->orderBy('title')->get();
        $subjects = Subject::where('course_id', 2)->orderBy('title')->get();
        $country_codes = \App\Helpers\CountriesHelper::get_country_code();

        return view('admin.converted-leads.support-bosse-index', compact(
            'convertedLeads', 
            'batches', 
            'subjects', 
            'country_codes'
        ));
    }

    /**
     * Show BOSSE support converted lead details
     */
    public function showBosse($id)
    {
        $convertedLead = ConvertedLead::with([
            'lead',
            'course',
            'academicAssistant',
            'createdBy',
            'studentDetails',
            'supportDetails',
            'subject',
            'batch',
            'admissionBatch'
        ])->findOrFail($id);

        if ((int) ($convertedLead->course_id) !== 2) {
            abort(404);
        }

        return view('admin.converted-leads.support-bosse-show', compact('convertedLead'));
    }

    /**
     * Show NIOS support converted lead details
     */
    public function showNios($id)
    {
        $convertedLead = ConvertedLead::with([
            'lead',
            'course',
            'academicAssistant',
            'createdBy',
            'studentDetails',
            'supportDetails',
            'subject',
            'batch',
            'admissionBatch'
        ])->findOrFail($id);

        if ((int) ($convertedLead->course_id) !== 1) {
            abort(404);
        }

        return view('admin.converted-leads.support-nios-show', compact('convertedLead'));
    }

    /**
     * Show unified support converted lead details (any course)
     */
    public function show($id)
    {
        $convertedLead = ConvertedLead::with([
            'lead',
            'course',
            'academicAssistant',
            'createdBy',
            'studentDetails',
            'supportDetails',
            'supportFeedbackHistory.createdBy',
            'subject',
            'batch',
            'admissionBatch'
        ])->findOrFail($id);

        return view('admin.converted-leads.support-show', compact('convertedLead'));
    }

    /**
     * Display a listing of NIOS converted leads for support
     */
    public function niosIndex(Request $request)
    {
        $query = ConvertedLead::with([
            'lead', 
            'course', 
            'academicAssistant', 
            'createdBy', 
            'studentDetails',
            'supportDetails',
            'subject',
            'batch',
            'admissionBatch'
        ])->where('course_id', 1); // NIOS course

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                        $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                    });
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Can see all
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('studentDetails', function($subQ) use ($search) {
                      $subQ->where('application_number', 'like', "%{$search}%");
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

        if ($request->filled('registration_status')) {
            $query->whereHas('supportDetails', function($q) use ($request) {
                $q->where('registration_status', $request->registration_status);
            });
        }

        if ($request->filled('student_status')) {
            $query->whereHas('supportDetails', function($q) use ($request) {
                $q->where('student_status', $request->student_status);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $convertedLeads = $query->orderBy('created_at', 'desc')->paginate(50);
        $batches = Batch::where('course_id', 1)->orderBy('title')->get();
        $subjects = Subject::where('course_id', 1)->orderBy('title')->get();
        $country_codes = \App\Helpers\CountriesHelper::get_country_code();

        return view('admin.converted-leads.support-nios-index', compact(
            'convertedLeads', 
            'batches', 
            'subjects', 
            'country_codes'
        ));
    }

    /**
     * Update support details inline
     */
    public function updateSupportDetails(Request $request, $id)
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

            // Handle all fields - update in converted_student_support_details table
            $supportDetails = $convertedLead->supportDetails;
            if (!$supportDetails) {
                $supportDetails = new ConvertedStudentSupportDetail();
                $supportDetails->converted_student_id = $id;
            }
            $supportDetails->$field = $value;
            $supportDetails->save();

            // Format the response value
            $responseValue = $this->formatResponseValue($field, $value);

            return response()->json([
                'success' => true,
                'message' => 'Updated successfully',
                'value' => $responseValue
            ]);

        } catch (\Exception $e) {
            Log::error('SupportConvertedLeadController updateSupportDetails error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while updating the record'
            ], 500);
        }
    }

    /**
     * Get validation rules for specific fields
     */
    private function getValidationRules($field)
    {
        $rules = [
            'registration_status' => 'nullable|string|max:255',
            'technology_side' => 'nullable|string|max:255',
            'student_status' => 'nullable|string|max:255',
            'call_1' => 'nullable|string|max:255',
            'app' => 'nullable|string|max:255',
            'whatsapp_group' => 'nullable|string|max:255',
            'telegram_group' => 'nullable|string|max:255',
            'problems' => 'nullable|string|max:500',
            'support_notes' => 'nullable|string|max:1000',
            'support_status' => 'nullable|string|max:255',
            'support_priority' => 'nullable|string|max:255',
        ];

        return $rules[$field] ?? null;
    }

    /**
     * Submit feedback for a converted lead
     */
    public function submitFeedback(Request $request, $id)
    {
        try {
            $convertedLead = ConvertedLead::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'feedback_type' => 'required|string|max:255',
                'feedback_content' => 'required|string|max:2000',
                'feedback_status' => 'nullable|string|max:255',
                'priority' => 'nullable|string|max:255',
                'follow_up_date' => 'nullable|date',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create feedback history record
            $feedback = SupportFeedbackHistory::create([
                'converted_student_id' => $id,
                'created_by' => AuthHelper::getCurrentUserId(),
                'feedback_type' => $request->feedback_type,
                'feedback_content' => $request->feedback_content,
                'feedback_status' => $request->feedback_status ?? 'pending',
                'priority' => $request->priority ?? 'medium',
                'follow_up_date' => $request->follow_up_date,
                'notes' => $request->notes,
            ]);

            // Update last_feedback timestamp in support details
            $supportDetails = $convertedLead->supportDetails;
            if (!$supportDetails) {
                $supportDetails = new ConvertedStudentSupportDetail();
                $supportDetails->converted_student_id = $id;
            }
            $supportDetails->last_feedback = now();
            $supportDetails->save();

            return response()->json([
                'success' => true,
                'message' => 'Feedback submitted successfully',
                'feedback' => $feedback->load('createdBy')
            ]);

        } catch (\Exception $e) {
            Log::error('SupportConvertedLeadController submitFeedback error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while submitting feedback'
            ], 500);
        }
    }

    /**
     * Display a listing of Hotel Management converted leads for support
     */
    public function hotelManagementIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 8, 'Hotel Management Converted Support List', 'admin.converted-leads.support-hotel-management-index');
    }

    /**
     * Display a listing of GMVSS converted leads for support
     */
    public function gmvssIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 16, 'GMVSS Converted Support List', 'admin.converted-leads.support-gmvss-index');
    }

    /**
     * Display a listing of AI with Python converted leads for support
     */
    public function aiPythonIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 10, 'AI with Python Converted Support List', 'admin.converted-leads.support-ai-python-index');
    }

    /**
     * Display a listing of Digital Marketing converted leads for support
     */
    public function digitalMarketingIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 11, 'Digital Marketing Converted Support List', 'admin.converted-leads.support-digital-marketing-index');
    }

    /**
     * Display a listing of AI Automation converted leads for support
     */
    public function aiAutomationIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 12, 'AI Automation Converted Support List', 'admin.converted-leads.support-ai-automation-index');
    }

    /**
     * Display a listing of Web Development converted leads for support
     */
    public function webDevelopmentIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 13, 'Web Development & Designing Converted Support List', 'admin.converted-leads.support-web-development-index');
    }

    /**
     * Display a listing of Vibe Coding converted leads for support
     */
    public function vibeCodingIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 14, 'Vibe Coding Converted Support List', 'admin.converted-leads.support-vibe-coding-index');
    }

    /**
     * Display a listing of Graphic Designing converted leads for support
     */
    public function graphicDesigningIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 15, 'Graphic Designing Converted Support List', 'admin.converted-leads.support-graphic-designing-index');
    }

    /**
     * Display a listing of Eduthanzeel converted leads for support
     */
    public function eduthanzeelIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 6, 'Eduthanzeel Converted Support List', 'admin.converted-leads.support-eduthanzeel-index');
    }

    /**
     * Display a listing of E-School converted leads for support
     */
    public function eSchoolIndex(Request $request)
    {
        return $this->getCourseSupportIndex($request, 5, 'E-School Converted Support List', 'admin.converted-leads.support-e-school-index');
    }

    /**
     * Generic method to get course support index
     */
    private function getCourseSupportIndex(Request $request, $courseId, $pageTitle, $viewName)
    {
        $query = ConvertedLead::with([
            'lead', 
            'course', 
            'academicAssistant', 
            'createdBy', 
            'studentDetails',
            'supportDetails',
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
                    $query->whereHas('lead', function($q) use ($teamMemberIds) {
                        $q->whereIn('telecaller_id', $teamMemberIds);
                    });
                } else {
                    $query->whereHas('lead', function($q) {
                        $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                    });
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Can see all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Can see all
            } elseif (RoleHelper::is_telecaller()) {
                $query->whereHas('lead', function($q) {
                    $q->where('telecaller_id', AuthHelper::getCurrentUserId());
                });
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('studentDetails', function($subQ) use ($search) {
                      $subQ->where('application_number', 'like', "%{$search}%");
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

        if ($request->filled('registration_status')) {
            $query->whereHas('supportDetails', function($q) use ($request) {
                $q->where('registration_status', $request->registration_status);
            });
        }

        if ($request->filled('student_status')) {
            $query->whereHas('supportDetails', function($q) use ($request) {
                $q->where('student_status', $request->student_status);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $convertedLeads = $query->orderBy('created_at', 'desc')->paginate(50);
        $batches = Batch::where('course_id', $courseId)->orderBy('title')->get();
        $subjects = Subject::where('course_id', $courseId)->orderBy('title')->get();
        $country_codes = \App\Helpers\CountriesHelper::get_country_code();

        return view($viewName, compact(
            'convertedLeads', 
            'batches', 
            'subjects', 
            'country_codes',
            'pageTitle'
        ));
    }

    /**
     * Format response value for display
     */
    private function formatResponseValue($field, $value)
    {
        return $value;
    }
}
