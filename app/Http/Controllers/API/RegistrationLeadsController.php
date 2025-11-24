<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadDetail;
use App\Models\LeadStatus;
use App\Models\LeadSource;
use App\Models\Course;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RegistrationLeadsController extends Controller
{
    /**
     * List registration-form-submitted leads with lazy loading & filters.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!$this->canAccessRegistrationData($user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied.',
            ], 403);
        }

        $query = $this->buildBaseQuery();
        $this->applyFilters($query, $request);
        $this->applyRoleRestrictions($query, $user, $request);

        $filteredCount = (clone $query)->count();
        $page = max(1, (int) $request->get('page', 1));
        $perPage = max(1, min(100, (int) $request->get('per_page', 25)));

        $leads = (clone $query)
            ->orderBy('id', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $data = $leads->map(function (Lead $lead) {
            return $this->transformLead($lead);
        });

        $counts = $this->calculateRegistrationCounts($user, $request);

        return response()->json([
            'status' => true,
            'data' => [
                'leads' => $data,
                'counts' => $counts,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $filteredCount,
                    'last_page' => $perPage > 0 ? (int) ceil($filteredCount / $perPage) : 0,
                    'from' => $filteredCount > 0 ? (($page - 1) * $perPage) + 1 : 0,
                    'to' => min($page * $perPage, $filteredCount),
                ],
            ],
        ]);
    }

    /**
     * Provide filter metadata for registration leads list.
     */
    public function filters(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!$this->canAccessRegistrationData($user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied.',
            ], 403);
        }

        $leadStatuses = LeadStatus::select('id', 'title')
            ->orderBy('title')
            ->get()
            ->map(function ($status) {
                return [
                    'value' => $status->id,
                    'label' => $status->title,
                ];
            });

        $leadSources = LeadSource::select('id', 'title')
            ->orderBy('title')
            ->get()
            ->map(function ($source) {
                return [
                    'value' => $source->id,
                    'label' => $source->title,
                ];
            });

        $courses = Course::select('id', 'title')
            ->orderBy('title')
            ->get()
            ->map(function ($course) {
                return [
                    'value' => $course->id,
                    'label' => $course->title,
                ];
            });

        $telecallersQuery = User::nonMarketingTelecallers()
            ->select('id', 'name', 'team_id')
            ->orderBy('name');

        if ($user->role_id == 3 && !$user->is_team_lead) {
            $telecallersQuery->where('id', $user->id);
        } elseif ($user->is_team_lead && $user->team_id) {
            $telecallersQuery->where('team_id', $user->team_id);
        }

        $telecallers = $telecallersQuery->get()->map(function ($telecaller) {
            return [
                'value' => $telecaller->id,
                'label' => $telecaller->name,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'filters' => [
                    'lead_statuses' => $leadStatuses,
                    'lead_sources' => $leadSources,
                    'courses' => $courses,
                    'telecallers' => $telecallers,
                    'registration_statuses' => [
                        ['value' => 'pending', 'label' => 'Pending'],
                        ['value' => 'approved', 'label' => 'Approved'],
                        ['value' => 'rejected', 'label' => 'Rejected'],
                        ['value' => 'all', 'label' => 'All'],
                    ],
                    'default_registration_status' => 'pending',
                    'can_filter_by_telecaller' => $user->role_id != 3 || $user->is_team_lead,
                ],
            ],
        ]);
    }

    /**
     * Detailed registration data for a single lead.
     */
    public function show(Request $request, $leadId)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!$this->canAccessRegistrationData($user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied.',
            ], 403);
        }

        $lead = Lead::with([
            'leadStatus:id,title',
            'leadSource:id,title',
            'course:id,title,needs_time',
            'telecaller:id,name,team_id',
            'team:id,title',
            'studentDetails' => function ($query) {
                $query->with([
                    'course:id,title,needs_time',
                    'subject:id,title',
                    'batch:id,title',
                    'subCourse:id,title',
                    'classTime:id,title,start_time,end_time',
                    'university:id,title',
                    'universityCourse:id,title',
                    'reviewedBy:id,name',
                    'sslcCertificates:id,lead_detail_id,file_path,verification_status,verified_by',
                    'sslcCertificates.verifiedBy:id,name',
                ]);
            },
        ])->findOrFail($leadId);

        if (!$this->canViewLead($lead, $user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied for this lead.',
            ], 403);
        }

        $studentDetail = $lead->studentDetails;

        if (!$studentDetail) {
            return response()->json([
                'status' => false,
                'message' => 'Registration details not found for this lead.',
            ], 404);
        }

        $detailData = [
            'lead' => $this->transformLead($lead),
            'student_detail' => $this->transformStudentDetail($studentDetail),
            'documents' => $this->buildDocumentPayload($studentDetail),
        ];

        return response()->json([
            'status' => true,
            'data' => $detailData,
        ]);
    }

    /**
     * Build the base query with eager loaded relationships.
     */
    private function buildBaseQuery()
    {
        return Lead::select([
                'id',
                'title',
                'code',
                'phone',
                'email',
                'lead_status_id',
                'lead_source_id',
                'course_id',
                'telecaller_id',
                'team_id',
                'place',
                'rating',
                'interest_status',
                'followup_date',
                'remarks',
                'is_converted',
                'created_at',
                'updated_at',
            ])
            ->with([
                'leadStatus:id,title',
                'leadSource:id,title',
                'course:id,title',
                'telecaller:id,name,team_id',
                'studentDetails' => function ($query) {
                    $query->select([
                        'id',
                        'lead_id',
                        'status',
                        'course_id',
                        'subject_id',
                        'batch_id',
                        'class_time_id',
                        'sub_course_id',
                        'sslc_certificate',
                        'plustwo_certificate',
                        'ug_certificate',
                        'post_graduation_certificate',
                        'birth_certificate',
                        'passport_photo',
                        'adhar_front',
                        'adhar_back',
                        'signature',
                        'other_document',
                        'sslc_verification_status',
                        'plustwo_verification_status',
                        'ug_verification_status',
                        'post_graduation_certificate_verification_status',
                        'birth_certificate_verification_status',
                        'passport_photo_verification_status',
                        'adhar_front_verification_status',
                        'adhar_back_verification_status',
                        'signature_verification_status',
                        'other_document_verification_status',
                        'admin_remarks',
                        'reviewed_by',
                        'reviewed_at',
                    ])->with([
                        'course:id,title',
                        'subject:id,title',
                        'batch:id,title',
                        'subCourse:id,title',
                        'classTime:id,title,start_time,end_time',
                        'reviewedBy:id,name',
                    ]);
                },
                'leadActivities' => function ($query) {
                    $query->select('id', 'lead_id', 'reason', 'created_at', 'activity_type')
                        ->whereNotNull('reason')
                        ->where('reason', '!=', '')
                        ->orderByDesc('created_at');
                },
            ])
            ->whereHas('studentDetails')
            ->notConverted()
            ->notDropped();
    }

    /**
     * Apply filters from request.
     */
    private function applyFilters($query, Request $request, $options = [])
    {
        $skipRegistrationStatus = $options['skip_registration_status'] ?? false;

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        if ($request->filled('lead_status_id')) {
            $query->where('lead_status_id', $request->lead_status_id);
        }

        if ($request->filled('lead_source_id')) {
            $query->where('lead_source_id', $request->lead_source_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('telecaller_id')) {
            $query->where('telecaller_id', $request->telecaller_id);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if (!$skipRegistrationStatus) {
            $registrationStatus = $request->get('registration_status', 'pending');

            if (in_array($registrationStatus, ['pending', 'approved', 'rejected'])) {
                $query->whereHas('studentDetails', function ($q) use ($registrationStatus) {
                    $q->where('status', $registrationStatus);
                });
            }
        }

        if ($request->filled('search_key')) {
            $searchKey = $request->search_key;
            $query->where(function ($q) use ($searchKey) {
                $q->where('title', 'like', "%{$searchKey}%")
                    ->orWhere('phone', 'like', "%{$searchKey}%")
                    ->orWhere('email', 'like', "%{$searchKey}%");
            });
        }
    }

    /**
     * Restrict query data based on user role.
     */
    private function applyRoleRestrictions($query, $user, Request $request)
    {
        if ($user->is_team_lead) {
            if ($user->team_id) {
                $teamMemberIds = User::where('team_id', $user->team_id)
                    ->where('role_id', 3)
                    ->whereNull('deleted_at')
                    ->pluck('id')
                    ->toArray();
                $teamMemberIds[] = $user->id;
                $query->whereIn('telecaller_id', $teamMemberIds);
            } else {
                $query->where('telecaller_id', $user->id);
            }
        } elseif ($user->role_id == 3) {
            $query->where('telecaller_id', $user->id);
        } elseif ($request->filled('telecaller_id')) {
            $query->where('telecaller_id', $request->telecaller_id);
        }
    }

    /**
     * Determine if the user can view registration data.
     */
    private function canAccessRegistrationData($user): bool
    {
        $allowedRoleIds = [1, 2, 3, 4, 5]; // super admin, admin, telecaller, admission counsellor, academic assistant

        return in_array($user->role_id, $allowedRoleIds)
            || $user->is_team_lead
            || $user->is_senior_manager;
    }

    /**
     * Restrict lead detail view based on role/ownership.
     */
    private function canViewLead(Lead $lead, $user): bool
    {
        if ($this->canAccessRegistrationData($user)) {
            if ($user->role_id == 3 && !$user->is_team_lead) {
                return (int) $lead->telecaller_id === (int) $user->id;
            }

            if ($user->is_team_lead && $user->team_id) {
                return (int) $lead->telecaller?->team_id === (int) $user->team_id
                    || (int) $lead->telecaller_id === (int) $user->id;
            }

            return true;
        }

        return false;
    }

    /**
     * Transform lead for API response.
     */
    private function transformLead(Lead $lead): array
    {
        $studentDetail = $lead->studentDetails;
        $latestActivity = $lead->leadActivities->first();

        return [
            'id' => $lead->id,
            'name' => $lead->title,
            'phone' => $this->formatPhone($lead->code, $lead->phone),
            'email' => $lead->email,
            'course' => $lead->course ? $lead->course->title : null,
            'lead_status' => $lead->leadStatus ? $lead->leadStatus->title : null,
            'lead_source' => $lead->leadSource ? $lead->leadSource->title : null,
            'telecaller' => $lead->telecaller ? $lead->telecaller->name : null,
            'telecaller_id' => $lead->telecaller_id,
            'rating' => $lead->rating,
            'interest_status' => $lead->interest_status,
            'registration_status' => $studentDetail ? $studentDetail->status : null,
            'admin_remarks' => $studentDetail?->admin_remarks,
            'last_activity' => $latestActivity ? [
                'reason' => $latestActivity->reason,
                'created_at' => $latestActivity->created_at?->format('Y-m-d H:i:s'),
            ] : null,
            'documents_summary' => $this->buildDocumentSummary($studentDetail),
            'submitted_at' => $studentDetail ? optional($studentDetail->created_at)->format('Y-m-d H:i:s') : null,
            'created_at' => $lead->created_at ? $lead->created_at->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     * Prepare student detail payload for detail endpoint.
     */
    private function transformStudentDetail(LeadDetail $detail): array
    {
        return [
            'id' => $detail->id,
            'status' => $detail->status,
            'course' => $detail->course ? $detail->course->title : null,
            'subject' => $detail->subject ? $detail->subject->title : null,
            'batch' => $detail->batch ? $detail->batch->title : null,
            'sub_course' => $detail->subCourse ? $detail->subCourse->title : null,
            'class_time' => $detail->classTime ? [
                'title' => $detail->classTime->title,
                'start_time' => $detail->classTime->start_time,
                'end_time' => $detail->classTime->end_time,
            ] : null,
            'second_language' => $detail->second_language,
            'passed_year' => $detail->passed_year,
            'programme_type' => $detail->programme_type,
            'student_name' => $detail->student_name,
            'father_name' => $detail->father_name,
            'mother_name' => $detail->mother_name,
            'date_of_birth' => $detail->date_of_birth ? $detail->date_of_birth->format('Y-m-d') : null,
            'gender' => $detail->gender,
            'contact' => [
                'personal' => $this->formatPhone($detail->personal_code, $detail->personal_number),
                'parents' => $this->formatPhone($detail->parents_code, $detail->parents_number),
                'father' => $this->formatPhone($detail->father_contact_code, $detail->father_contact_number),
                'mother' => $this->formatPhone($detail->mother_contact_code, $detail->mother_contact_number),
                'whatsapp' => $this->formatPhone($detail->whatsapp_code, $detail->whatsapp_number),
            ],
            'address' => [
                'street' => $detail->street,
                'locality' => $detail->locality,
                'post_office' => $detail->post_office,
                'district' => $detail->district,
                'state' => $detail->state,
                'pin_code' => $detail->pin_code,
            ],
            'admin_remarks' => $detail->admin_remarks,
            'reviewed_by' => $detail->reviewedBy ? $detail->reviewedBy->name : null,
            'reviewed_at' => $detail->reviewed_at ? $detail->reviewed_at->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     * Summaries of uploaded documents & verification status.
     */
    private function buildDocumentSummary(?LeadDetail $detail): array
    {
        if (!$detail) {
            return [];
        }

        $documentTypes = [
            'sslc_certificate' => 'SSLC Certificate',
            'plustwo_certificate' => 'Plus Two Certificate',
            'ug_certificate' => 'UG Certificate',
            'post_graduation_certificate' => 'Post Graduation Certificate',
            'birth_certificate' => 'Birth Certificate',
            'passport_photo' => 'Passport Photo',
            'adhar_front' => 'Aadhar Front',
            'adhar_back' => 'Aadhar Back',
            'signature' => 'Signature',
            'other_document' => 'Other Document',
        ];

        $summary = [];

        foreach ($documentTypes as $field => $label) {
            $statusField = $field . '_verification_status';
            $summary[$field] = [
                'label' => $label,
                'uploaded' => !empty($detail->$field),
                'status' => $detail->$statusField ?? null,
            ];
        }

        return $summary;
    }

    /**
     * Build document payload for detail endpoint.
     */
    private function buildDocumentPayload(LeadDetail $detail): array
    {
        $documentTypes = [
            'sslc_certificate' => 'SSLC Certificate',
            'plustwo_certificate' => 'Plus Two Certificate',
            'ug_certificate' => 'UG Certificate',
            'post_graduation_certificate' => 'Post Graduation Certificate',
            'birth_certificate' => 'Birth Certificate',
            'passport_photo' => 'Passport Photo',
            'adhar_front' => 'Aadhar Front',
            'adhar_back' => 'Aadhar Back',
            'signature' => 'Signature',
            'other_document' => 'Other Document',
        ];

        $documents = [];

        foreach ($documentTypes as $field => $label) {
            $statusField = $field . '_verification_status';
            $verifiedByField = $field . '_verified_by';
            $verifiedAtField = $field . '_verified_at';

            $documents[$field] = [
                'label' => $label,
                'url' => $this->buildFileUrl($detail->$field),
                'status' => $detail->$statusField ?? null,
                'verified_by' => $detail->$verifiedByField,
                'verified_at' => $detail->$verifiedAtField ? $detail->$verifiedAtField->format('Y-m-d H:i:s') : null,
            ];
        }

        if ($detail->sslcCertificates && $detail->sslcCertificates->count() > 0) {
            $documents['sslc_multiple'] = $detail->sslcCertificates->map(function ($certificate) {
                return [
                    'url' => $this->buildFileUrl($certificate->file_path ?? null),
                    'status' => $certificate->verification_status,
                    'verified_by' => $certificate->verifiedBy ? $certificate->verifiedBy->name : null,
                ];
            });
        }

        return $documents;
    }

    /**
     * Build a usable file URL.
     */
    private function buildFileUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    /**
     * Format phone number with code.
     */
    private function formatPhone(?string $code, ?string $number): ?string
    {
        if (!$number) {
            return null;
        }

        if ($code) {
            return '+' . ltrim($code, '+') . ' ' . $number;
        }

        return $number;
    }

    /**
     * Calculate counts for registration statuses.
     */
    private function calculateRegistrationCounts($user, Request $request): array
    {
        $baseQuery = $this->buildBaseQuery();
        $this->applyFilters($baseQuery, $request, ['skip_registration_status' => true]);
        $this->applyRoleRestrictions($baseQuery, $user, $request);

        $allCount = (clone $baseQuery)->count();
        $pendingCount = (clone $baseQuery)->whereHas('studentDetails', function ($q) {
            $q->where('status', 'pending');
        })->count();
        $approvedCount = (clone $baseQuery)->whereHas('studentDetails', function ($q) {
            $q->where('status', 'approved');
        })->count();
        $rejectedCount = (clone $baseQuery)->whereHas('studentDetails', function ($q) {
            $q->where('status', 'rejected');
        })->count();

        return [
            'all' => $allCount,
            'pending' => $pendingCount,
            'approved' => $approvedCount,
            'rejected' => $rejectedCount,
        ];
    }
}


