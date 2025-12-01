<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ConvertedLead;
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
     * Apply role-based filtering to the query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\Models\User $user
     * @return void
     */
    private function applyRoleBasedFilter($query, $user)
    {
        // Set current user for role helpers
        AuthHelper::setCurrentUser($user);

        // General Manager: Can see ALL converted leads (no filter)
        if (RoleHelper::is_general_manager()) {
            // No filtering
        // Check team lead next
        } elseif (RoleHelper::is_team_lead()) {
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
        } elseif (RoleHelper::is_admission_counsellor()) {
            // Admission Counsellor: Can see ALL converted leads
            // No additional filtering needed - show all
        } elseif (RoleHelper::is_academic_assistant()) {
            // Academic Assistant: Can see ALL converted leads
            // No additional filtering needed - show all
        } elseif (RoleHelper::is_telecaller()) {
            // Telecaller: Can only see converted leads from leads assigned to them
            $query->whereHas('lead', function($q) use ($user) {
                $q->where('telecaller_id', $user->id);
            });
        } elseif (RoleHelper::is_support_team()) {
            // Support Team: Only see academically verified leads
            $query->where('is_academic_verified', 1);
        } elseif (RoleHelper::is_mentor()) {
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

