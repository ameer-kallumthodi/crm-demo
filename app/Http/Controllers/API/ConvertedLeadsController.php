<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ConvertedLead;
use App\Models\LeadActivity;
use App\Models\ConvertedStudentActivity;
use App\Services\LeadCallLogService;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use App\Helpers\PhoneNumberHelper;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ConvertedLeadsController extends Controller
{
    /**
     * Get converted leads list with lazy loading (pagination) and filters
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Base query with relationships
        $query = ConvertedLead::with([
            'lead:id,telecaller_id',
            'course:id,title',
            'academicAssistant:id,name',
            'createdBy:id,name',
            'subject:id,title',
            'studentDetails',
            'leadDetail:lead_id,reviewed_at',
            'batch:id,title',
            'admissionBatch:id,title',
        ]);

        // Apply role-based filtering (same as web controller)
        $this->applyRoleBasedFilter($query, $user);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('register_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('admission_batch_id')) {
            $query->where('admission_batch_id', $request->admission_batch_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('reg_fee')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('reg_fee', $request->reg_fee);
            });
        }

        if ($request->filled('exam_fee')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('exam_fee', $request->exam_fee);
            });
        }

        if ($request->filled('id_card')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('id_card', $request->id_card);
            });
        }

        if ($request->filled('tma')) {
            $query->whereHas('studentDetails', function($q) use ($request) {
                $q->where('tma', $request->tma);
            });
        }

        // Apply date filtering
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Order by created_at desc
        $query->orderBy('created_at', 'desc');

        // Pagination - lazy loading
        $page = max(1, (int) $request->get('page', 1));
        $perPage = max(1, min(100, (int) $request->get('per_page', 25)));
        
        $convertedLeads = $query->paginate($perPage, ['*'], 'page', $page);

        // Format converted leads data
        $appTimezone = config('app.timezone');
        
        // Collect all verified_by user IDs to avoid N+1 queries
        $academicVerifiedByIds = $convertedLeads->pluck('academic_verified_by')->filter()->unique()->values();
        $supportVerifiedByIds = $convertedLeads->pluck('support_verified_by')->filter()->unique()->values();
        $allUserIds = $academicVerifiedByIds->merge($supportVerifiedByIds)->unique()->values();
        
        // Load all users at once
        $users = \App\Models\User::whereIn('id', $allUserIds)->get()->keyBy('id');
        
        $formattedLeads = $convertedLeads->map(function ($convertedLead) use ($appTimezone, $users) {
            return $this->formatConvertedLeadData($convertedLead, $appTimezone, $users);
        });

        return response()->json([
            'status' => true,
            'data' => $formattedLeads,
            'pagination' => [
                'current_page' => $convertedLeads->currentPage(),
                'per_page' => $convertedLeads->perPage(),
                'total' => $convertedLeads->total(),
                'last_page' => $convertedLeads->lastPage(),
                'from' => $convertedLeads->firstItem(),
                'to' => $convertedLeads->lastItem(),
            ]
        ], 200);
    }

    /**
     * Get converted lead details (same as web page view)
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Load converted lead with all relationships (same as web controller)
        $convertedLead = ConvertedLead::with([
            'lead',
            'lead.telecaller:id,name',
            'leadDetail.sslcCertificates.verifiedBy:id,name',
            'leadDetail.sslcVerifiedBy:id,name',
            'leadDetail.plustwoVerifiedBy:id,name',
            'leadDetail.ugVerifiedBy:id,name',
            'leadDetail.passportPhotoVerifiedBy:id,name',
            'leadDetail.adharFrontVerifiedBy:id,name',
            'leadDetail.adharBackVerifiedBy:id,name',
            'leadDetail.signatureVerifiedBy:id,name',
            'leadDetail.birthCertificateVerifiedBy:id,name',
            'leadDetail.otherDocumentVerifiedBy:id,name',
            'course',
            'batch',
            'admissionBatch',
            'subject',
            'academicAssistant',
            'createdBy',
            'studentDetails.registrationLink'
        ])->find($id);

        if (!$convertedLead) {
            return response()->json([
                'status' => false,
                'message' => 'Converted lead not found'
            ], 404);
        }

        // Check access permissions (same as web controller)
        if (!$this->canAccessConvertedLead($convertedLead, $user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied. You do not have permission to view this converted lead.'
            ], 403);
        }

        // Get lead activities (exclude pullbacked activities, same as web controller)
        $leadActivities = LeadActivity::where('lead_id', $convertedLead->lead_id)
            ->where(function ($query) {
                $query->whereNull('is_pullbacked')
                      ->orWhere('is_pullbacked', 0);
            })
            ->select('id', 'lead_id', 'reason', 'created_at', 'activity_type', 'description', 'remarks', 'rating', 'followup_date', 'created_by', 'lead_status_id')
            ->with(['leadStatus:id,title', 'createdBy:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get converted student activities
        $convertedStudentActivities = ConvertedStudentActivity::where('converted_lead_id', $convertedLead->id)
            ->with(['createdBy:id,name'])
            ->orderBy('activity_date', 'desc')
            ->orderBy('activity_time', 'desc')
            ->get();

        // Get call logs
        $callLogs = LeadCallLogService::forConvertedLead($convertedLead);

        // Format data for API response
        $appTimezone = config('app.timezone');
        $formattedData = $this->formatConvertedLeadDetailData($convertedLead, $leadActivities, $convertedStudentActivities, $callLogs, $appTimezone);

        return response()->json([
            'status' => true,
            'data' => $formattedData
        ], 200);
    }

    /**
     * Check if user can access the converted lead
     *
     * @param ConvertedLead $convertedLead
     * @param \App\Models\User $user
     * @return bool
     */
    private function canAccessConvertedLead($convertedLead, $user)
    {
        $roleId = $user->role_id;
        $isTeamLead = $user->is_team_lead == 1;
        $role = \App\Models\UserRole::find($roleId);
        $roleTitle = $role ? $role->title : '';

        // General Manager, Admin, Admission Counsellor, Academic Assistant: Can see all
        if ($roleTitle === 'General Manager' || 
            $roleId == 1 || // Super Admin
            $roleId == 2 || // Admin
            $roleTitle === 'Admission Counsellor' || 
            $roleTitle === 'Academic Assistant') {
            return true;
        }

        // Team Lead: Can see converted leads from their team
        if ($isTeamLead) {
            $teamId = $user->team_id;
            if ($teamId) {
                $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                return in_array($convertedLead->lead->telecaller_id, $teamMemberIds);
            } else {
                return $convertedLead->lead->telecaller_id == $user->id;
            }
        }

        // Telecaller: Can only see converted leads from leads assigned to them
        if ($roleId == 3) {
            return $convertedLead->lead->telecaller_id == $user->id;
        }

        // Support Team: Can see academically verified leads
        if ($roleTitle === 'Support Team') {
            return $convertedLead->is_academic_verified == 1;
        }

        // Default: deny access
        return false;
    }

    /**
     * Format converted lead detail data for API response
     *
     * @param ConvertedLead $convertedLead
     * @param \Illuminate\Support\Collection $leadActivities
     * @param \Illuminate\Support\Collection $convertedStudentActivities
     * @param \Illuminate\Support\Collection $callLogs
     * @param string $appTimezone
     * @return array
     */
    private function formatConvertedLeadDetailData($convertedLead, $leadActivities, $convertedStudentActivities, $callLogs, $appTimezone)
    {
        // Format converted lead data (reuse existing method)
        $users = collect();
        $convertedLeadData = $this->formatConvertedLeadData($convertedLead, $appTimezone, $users);

        // Format lead activities
        $formattedLeadActivities = $leadActivities->map(function ($activity) use ($appTimezone) {
            return [
                'id' => $activity->id,
                'lead_id' => $activity->lead_id,
                'activity_type' => $activity->activity_type,
                'description' => $activity->description,
                'reason' => $activity->reason,
                'remarks' => $activity->remarks,
                'rating' => $activity->rating,
                'followup_date' => $activity->followup_date ? $activity->followup_date->format('Y-m-d') : null,
                'followup_date_display' => $activity->followup_date ? $activity->followup_date->format('d-m-Y') : null,
                'created_at' => $activity->created_at ? $activity->created_at->format('Y-m-d H:i:s') : null,
                'created_at_display' => $activity->created_at ? $activity->created_at->copy()->timezone($appTimezone)->format('d-m-Y h:i A') : null,
                'lead_status' => $activity->leadStatus ? [
                    'id' => $activity->leadStatus->id,
                    'title' => $activity->leadStatus->title,
                ] : null,
                'created_by' => $activity->createdBy ? [
                    'id' => $activity->createdBy->id,
                    'name' => $activity->createdBy->name,
                ] : null,
            ];
        });

        // Format converted student activities
        $formattedStudentActivities = $convertedStudentActivities->map(function ($activity) use ($appTimezone) {
            return [
                'id' => $activity->id,
                'converted_lead_id' => $activity->converted_lead_id,
                'activity_type' => $activity->activity_type,
                'activity_date' => $activity->activity_date ? $activity->activity_date->format('Y-m-d') : null,
                'activity_date_display' => $activity->activity_date ? $activity->activity_date->format('d-m-Y') : null,
                'activity_time' => $activity->activity_time ? $activity->activity_time->format('H:i:s') : null,
                'activity_time_display' => $activity->activity_time ? $activity->activity_time->format('h:i A') : null,
                'description' => $activity->description,
                'remarks' => $activity->remarks,
                'created_at' => $activity->created_at ? $activity->created_at->format('Y-m-d H:i:s') : null,
                'created_at_display' => $activity->created_at ? $activity->created_at->copy()->timezone($appTimezone)->format('d-m-Y h:i A') : null,
                'created_by' => $activity->createdBy ? [
                    'id' => $activity->createdBy->id,
                    'name' => $activity->createdBy->name,
                ] : null,
            ];
        });

        // Format call logs
        $formattedCallLogs = $callLogs->map(function ($callLog) use ($appTimezone) {
            return [
                'id' => $callLog->id,
                'type' => $callLog->type,
                'call_uuid' => $callLog->call_uuid,
                'called_number' => $callLog->calledNumber,
                'caller_number' => $callLog->callerNumber,
                'agent_number' => $callLog->AgentNumber,
                'extension_number' => $callLog->extensionNumber,
                'destination_number' => $callLog->destinationNumber,
                'callerid' => $callLog->callerid,
                'duration' => $callLog->duration,
                'formatted_duration' => $callLog->formatted_duration ?? $this->formatDuration($callLog->duration),
                'status' => $callLog->status,
                'date' => $callLog->date ? $callLog->date->format('Y-m-d') : null,
                'date_display' => $callLog->date ? $callLog->date->format('d-m-Y') : null,
                'start_time' => $callLog->start_time ? $callLog->start_time->format('H:i:s') : null,
                'start_time_display' => $callLog->start_time ? $callLog->start_time->format('h:i A') : null,
                'end_time' => $callLog->end_time ? $callLog->end_time->format('H:i:s') : null,
                'end_time_display' => $callLog->end_time ? $callLog->end_time->format('h:i A') : null,
                'recording_url' => $callLog->recording_URL,
                'telecaller_name' => $callLog->telecaller_name ?? null,
                'created_at' => $callLog->created_at ? $callLog->created_at->format('Y-m-d H:i:s') : null,
            ];
        });

        // Add lead information with more details
        $leadData = null;
        if ($convertedLead->lead) {
            $leadData = [
                'id' => $convertedLead->lead->id,
                'title' => $convertedLead->lead->title,
                'phone' => $convertedLead->lead->phone,
                'phone_code' => $convertedLead->lead->code,
                'phone_display' => PhoneNumberHelper::display($convertedLead->lead->code, $convertedLead->lead->phone),
                'whatsapp' => $convertedLead->lead->whatsapp,
                'whatsapp_code' => $convertedLead->lead->whatsapp_code,
                'email' => $convertedLead->lead->email,
                'telecaller' => $convertedLead->lead->telecaller ? [
                    'id' => $convertedLead->lead->telecaller->id,
                    'name' => $convertedLead->lead->telecaller->name,
                ] : null,
            ];
        }

        // Add lead detail information
        $leadDetailData = null;
        if ($convertedLead->leadDetail) {
            $leadDetailData = [
                'lead_id' => $convertedLead->leadDetail->lead_id,
                'reviewed_at' => $convertedLead->leadDetail->reviewed_at ? $convertedLead->leadDetail->reviewed_at->format('Y-m-d H:i:s') : null,
                'reviewed_at_display' => $convertedLead->leadDetail->reviewed_at ? $convertedLead->leadDetail->reviewed_at->copy()->timezone($appTimezone)->format('d-m-Y h:i A') : null,
                // Add other lead detail fields as needed
            ];
        }

        return [
            'converted_lead' => $convertedLeadData,
            'lead' => $leadData,
            'lead_detail' => $leadDetailData,
            'lead_activities' => $formattedLeadActivities,
            'converted_student_activities' => $formattedStudentActivities,
            'call_logs' => $formattedCallLogs,
        ];
    }

    /**
     * Format duration in seconds to readable format
     *
     * @param int|null $seconds
     * @return string
     */
    private function formatDuration($seconds)
    {
        if (!$seconds) {
            return 'N/A';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
        }

        return sprintf('%d:%02d', $minutes, $secs);
    }

    /**
     * Get converted leads filter data (courses, batches, admission batches, status options, etc.)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filters(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Get courses (active only, same as web page)
        $courses = \App\Models\Course::where('is_active', 1)
            ->select('id', 'title')
            ->orderBy('title')
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                ];
            });

        // Get batches (active only, same as web page)
        $batches = \App\Models\Batch::where('is_active', 1)
            ->select('id', 'title', 'course_id')
            ->orderBy('title')
            ->get()
            ->map(function ($batch) {
                return [
                    'id' => $batch->id,
                    'title' => $batch->title,
                    'course_id' => $batch->course_id,
                ];
            });

        // Get admission batches (active only, same as web page)
        $admissionBatches = \App\Models\AdmissionBatch::where('is_active', 1)
            ->select('id', 'title', 'batch_id')
            ->orderBy('title')
            ->get()
            ->map(function ($admissionBatch) {
                return [
                    'id' => $admissionBatch->id,
                    'title' => $admissionBatch->title,
                    'batch_id' => $admissionBatch->batch_id,
                ];
            });

        // Status options (same as web page)
        $statusOptions = [
            ['value' => 'Paid', 'label' => 'Paid'],
            ['value' => 'Admission cancel', 'label' => 'Admission cancel'],
            ['value' => 'Active', 'label' => 'Active'],
            ['value' => 'Inactive', 'label' => 'Inactive'],
        ];

        // Reg Fee options (same as web page)
        $regFeeOptions = [
            ['value' => 'Received', 'label' => 'Received'],
            ['value' => 'Not Received', 'label' => 'Not Received'],
        ];

        // Exam Fee options (same as web page)
        $examFeeOptions = [
            ['value' => 'Pending', 'label' => 'Pending'],
            ['value' => 'Not Paid', 'label' => 'Not Paid'],
            ['value' => 'Paid', 'label' => 'Paid'],
        ];

        // ID Card options (same as web page)
        $idCardOptions = [
            ['value' => 'processing', 'label' => 'processing'],
            ['value' => 'download', 'label' => 'download'],
            ['value' => 'not downloaded', 'label' => 'not downloaded'],
        ];

        // TMA options (same as web page)
        $tmaOptions = [
            ['value' => 'Uploaded', 'label' => 'Uploaded'],
            ['value' => 'Not Upload', 'label' => 'Not Upload'],
        ];

        return response()->json([
            'status' => true,
            'data' => [
                'courses' => $courses,
                'batches' => $batches,
                'admission_batches' => $admissionBatches,
                'status_options' => $statusOptions,
                'reg_fee_options' => $regFeeOptions,
                'exam_fee_options' => $examFeeOptions,
                'id_card_options' => $idCardOptions,
                'tma_options' => $tmaOptions,
            ]
        ], 200);
    }

    /**
     * Apply role-based filtering to the query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\Models\User $user
     * @return void
     */
    private function applyRoleBasedFilter($query, $user)
    {
        // Get user role to determine access
        $roleId = $user->role_id;
        $isTeamLead = $user->is_team_lead == 1;
        $isSeniorManager = $user->is_senior_manager == 1;
        
        // Get role title for specific role checks
        $role = \App\Models\UserRole::find($roleId);
        $roleTitle = $role ? $role->title : '';

        // General Manager: Can see ALL converted leads (no filter)
        if ($roleTitle === 'General Manager') {
            // No filtering
        // Check team lead next
        } elseif ($isTeamLead) {
            // Team Lead: Can see converted leads from their team
            $teamId = $user->team_id;
            if ($teamId) {
                $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                $query->whereHas('lead', function($q) use ($teamMemberIds) {
                    $q->whereIn('telecaller_id', $teamMemberIds);
                });
            } else {
                // If no team assigned, only show their own leads
                $query->whereHas('lead', function($q) use ($user) {
                    $q->where('telecaller_id', $user->id);
                });
            }
        } elseif ($roleTitle === 'Admission Counsellor') {
            // Admission Counsellor: Can see ALL converted leads
            // No additional filtering needed - show all
        } elseif ($roleTitle === 'Academic Assistant') {
            // Academic Assistant: Can see ALL converted leads
            // No additional filtering needed - show all
        } elseif ($roleId == 3) {
            // Telecaller: Can only see converted leads from leads assigned to them
            $query->whereHas('lead', function($q) use ($user) {
                $q->where('telecaller_id', $user->id);
            });
        } elseif ($roleTitle === 'Support Team') {
            // Support Team: Only see academically verified leads
            $query->where('is_academic_verified', 1);
        } elseif ($roleTitle === 'Mentor') {
            // Mentor: Filter by admission_batch_id where mentor_id matches
            // Currently commented out in web controller, so no filtering for now
        }
    }

    /**
     * Format converted lead data for API response
     *
     * @param ConvertedLead $convertedLead
     * @param string $appTimezone
     * @param \Illuminate\Support\Collection $users
     * @return array
     */
    private function formatConvertedLeadData($convertedLead, $appTimezone, $users = null)
    {
        // Format dates
        $academicVerifiedAt = $convertedLead->academic_verified_at
            ? $convertedLead->academic_verified_at->copy()->timezone($appTimezone)->format('d-m-Y h:i A')
            : null;

        $supportVerifiedAt = $convertedLead->support_verified_at
            ? $convertedLead->support_verified_at->copy()->timezone($appTimezone)->format('d-m-Y h:i A')
            : null;

        $academicDocumentApprovedAt = $convertedLead->leadDetail?->reviewed_at
            ? $convertedLead->leadDetail->reviewed_at->copy()->timezone($appTimezone)->format('d-m-Y h:i A')
            : null;

        $convertedDate = $convertedLead->studentDetails?->converted_date 
            ? Carbon::parse($convertedLead->studentDetails->converted_date)->format('d-m-Y')
            : $convertedLead->created_at->format('d-m-Y');

        // Format DOB
        $dobDisplay = $convertedLead->dob 
            ? (strtotime($convertedLead->dob) ? date('d-m-Y', strtotime($convertedLead->dob)) : $convertedLead->dob)
            : null;

        // Format phone number
        $phoneDisplay = PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone);

        return [
            'id' => $convertedLead->id,
            'lead_id' => $convertedLead->lead_id,
            'name' => $convertedLead->name,
            'phone' => $convertedLead->phone,
            'phone_code' => $convertedLead->code,
            'phone_display' => $phoneDisplay,
            'email' => $convertedLead->email,
            'dob' => $convertedLead->dob,
            'dob_display' => $dobDisplay,
            'register_number' => $convertedLead->register_number,
            'status' => $convertedLead->status,
            'converted_date' => $convertedDate,
            'created_at' => $convertedLead->created_at ? $convertedLead->created_at->format('Y-m-d H:i:s') : null,
            'created_at_display' => $convertedLead->created_at ? $convertedLead->created_at->format('d-m-Y h:i A') : null,
            'updated_at' => $convertedLead->updated_at ? $convertedLead->updated_at->format('Y-m-d H:i:s') : null,
            
            // Academic verification
            'is_academic_verified' => (bool) ($convertedLead->is_academic_verified ?? false),
            'academic_verified_at' => $convertedLead->academic_verified_at ? $convertedLead->academic_verified_at->format('Y-m-d H:i:s') : null,
            'academic_verified_at_display' => $academicVerifiedAt,
            'academic_verified_by_id' => $convertedLead->academic_verified_by,
            'academic_verified_by' => $convertedLead->academic_verified_by && $users 
                ? ($users->get($convertedLead->academic_verified_by)?->only(['id', 'name']) ?? null)
                : ($convertedLead->academic_verified_by ? \App\Models\User::find($convertedLead->academic_verified_by)?->only(['id', 'name']) : null),
            
            // Support verification
            'is_support_verified' => (bool) ($convertedLead->is_support_verified ?? false),
            'support_verified_at' => $convertedLead->support_verified_at ? $convertedLead->support_verified_at->format('Y-m-d H:i:s') : null,
            'support_verified_at_display' => $supportVerifiedAt,
            'support_verified_by_id' => $convertedLead->support_verified_by,
            'support_verified_by' => $convertedLead->support_verified_by && $users 
                ? ($users->get($convertedLead->support_verified_by)?->only(['id', 'name']) ?? null)
                : ($convertedLead->support_verified_by ? \App\Models\User::find($convertedLead->support_verified_by)?->only(['id', 'name']) : null),
            
            // Academic document approval
            'academic_document_approved_at' => $convertedLead->leadDetail?->reviewed_at 
                ? $convertedLead->leadDetail->reviewed_at->format('Y-m-d H:i:s') 
                : null,
            'academic_document_approved_at_display' => $academicDocumentApprovedAt,
            
            // Course information
            'course' => $convertedLead->course ? [
                'id' => $convertedLead->course->id,
                'title' => $convertedLead->course->title,
            ] : null,
            'course_id' => $convertedLead->course_id,
            
            // Batch information
            'batch' => $convertedLead->batch ? [
                'id' => $convertedLead->batch->id,
                'title' => $convertedLead->batch->title,
            ] : null,
            'batch_id' => $convertedLead->batch_id,
            
            // Admission batch information
            'admission_batch' => $convertedLead->admissionBatch ? [
                'id' => $convertedLead->admissionBatch->id,
                'title' => $convertedLead->admissionBatch->title,
            ] : null,
            'admission_batch_id' => $convertedLead->admission_batch_id,
            
            // Subject information
            'subject' => $convertedLead->subject ? [
                'id' => $convertedLead->subject->id,
                'title' => $convertedLead->subject->title,
            ] : null,
            'subject_id' => $convertedLead->subject_id,
            
            // Student details
            'student_details' => $convertedLead->studentDetails ? [
                'reg_fee' => $convertedLead->studentDetails->reg_fee,
                'exam_fee' => $convertedLead->studentDetails->exam_fee,
                'id_card' => $convertedLead->studentDetails->id_card,
                'tma' => $convertedLead->studentDetails->tma,
                'enroll_no' => $convertedLead->studentDetails->enroll_no,
                'converted_date' => $convertedLead->studentDetails->converted_date,
            ] : null,
            
            // Academic assistant
            'academic_assistant' => $convertedLead->academicAssistant ? [
                'id' => $convertedLead->academicAssistant->id,
                'name' => $convertedLead->academicAssistant->name,
            ] : null,
            'academic_assistant_id' => $convertedLead->academic_assistant_id,
            
            // Created by
            'created_by' => $convertedLead->createdBy ? [
                'id' => $convertedLead->createdBy->id,
                'name' => $convertedLead->createdBy->name,
            ] : null,
            'created_by_id' => $convertedLead->created_by,
        ];
    }
}

