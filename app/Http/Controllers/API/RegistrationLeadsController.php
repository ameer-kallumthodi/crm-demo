<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ClassTime;
use App\Models\Lead;
use App\Models\LeadDetail;
use App\Models\LeadStatus;
use App\Models\LeadSource;
use App\Models\Course;
use App\Models\Country;
use App\Models\User;
use App\Models\ConvertedLead;
use App\Models\Board;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Batch;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Filesystem\FilesystemAdapter;

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
        $this->applyFilters($query, $request, ['skip_registration_status' => true]); // Skip status filter to get all leads
        $this->applyRoleRestrictions($query, $user, $request);

        // Get all leads (no pagination) to group by status
        $allLeads = (clone $query)
            ->orderBy('id', 'desc')
            ->get();

        // Transform and group leads by registration status
        $groupedLeads = [
            'pending' => [],
            'approved' => [],
            'rejected' => [],
        ];

        foreach ($allLeads as $lead) {
            $transformedLead = $this->transformLead($lead);
            $status = $transformedLead['registration_status'] ?? 'pending';
            
            // Only include in grouped array if status is one of the expected values
            if (in_array($status, ['pending', 'approved', 'rejected'])) {
                $groupedLeads[$status][] = $transformedLead;
            }
        }

        $counts = $this->calculateRegistrationCounts($user, $request);

        return response()->json([
            'status' => true,
            'data' => [
                'leads' => $groupedLeads,
                'counts' => $counts,
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => $allLeads->count(),
                    'total' => $allLeads->count(),
                    'last_page' => 1,
                    'from' => $allLeads->count() > 0 ? 1 : 0,
                    'to' => $allLeads->count(),
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
                        ['value' => 'all', 'label' => 'All'],
                        ['value' => 'pending', 'label' => 'Pending'],
                        ['value' => 'approved', 'label' => 'Approved'],
                        ['value' => 'rejected', 'label' => 'Rejected'],
                    ],
                    'default_registration_status' => 'all',
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
            'team:id,name',
            'studentDetails' => function ($query) {
                $query->with([
                    'course:id,title,needs_time',
                    'subject:id,title',
                    'batch:id,title',
                    'subCourse:id,title',
                    'classTime:id,course_id,from_time,to_time',
                    'university:id,title',
                    'universityCourse:id,title',
                    'reviewedBy:id,name',
                    'sslcCertificates:id,lead_detail_id,certificate_path,verification_status,verified_by',
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
     * Fetch metadata required for converting a lead (mirrors web convert view).
     */
    public function convert(Request $request, Lead $lead)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!$this->canViewLead($lead, $user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied for this lead.',
            ], 403);
        }

        if ($lead->is_converted) {
            return response()->json([
                'status' => false,
                'message' => 'Lead already converted.',
            ], 409);
        }

        $lead->loadMissing([
            'studentDetails.course',
            'studentDetails.subject',
            'studentDetails.batch',
            'studentDetails.subCourse',
            'studentDetails.classTime',
            'studentDetails.university',
            'batch',
            'course',
        ]);

        $boards = Board::where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title'])
            ->map(function ($board) {
                return [
                    'value' => $board->id,
                    'label' => $board->title,
                ];
            });

        $countryCodes = get_country_code();

        $course = $lead->course;
        $batch = $lead->batch ?: ($lead->studentDetails?->batch);
        $courseAmount = $course ? (float) ($course->amount ?? 0) : 0.0;
        $batchAmount = $batch ? (float) ($batch->amount ?? 0) : 0.0;
        $extraAmount = 0.0;
        $universityAmount = 0.0;
        $courseType = null;
        $university = $lead->studentDetails?->university;

        if ($lead->course_id == 16 && $lead->studentDetails && $lead->studentDetails->class === 'sslc') {
            $extraAmount = 10000.0;
        }

        if ($lead->course_id == 9 && $lead->studentDetails) {
            $courseType = $lead->studentDetails->course_type;
            $universityId = $lead->studentDetails->university_id;

            if ($universityId) {
                $universityModel = $university ?: University::find($universityId);
                if ($universityModel) {
                    $university = $universityModel;
                    if ($courseType === 'UG') {
                        $universityAmount = (float) ($universityModel->ug_amount ?? 0);
                    } elseif ($courseType === 'PG') {
                        $universityAmount = (float) ($universityModel->pg_amount ?? 0);
                    }
                }
            }
        }

        $additionalAmount = $extraAmount + $universityAmount;
        $totalAmount = $courseAmount + $batchAmount + $additionalAmount;
        $dob = ($lead->studentDetails && $lead->studentDetails->date_of_birth)
            ? Carbon::parse($lead->studentDetails->date_of_birth)->format('Y-m-d')
            : null;

        return response()->json([
            'status' => true,
            'data' => [
                'lead' => [
                    'id' => $lead->id,
                    'name' => $lead->title,
                    'code' => $lead->code,
                    'phone' => $lead->phone,
                    'email' => $lead->email,
                    'dob' => $dob,
                    'course_id' => $lead->course_id,
                    'batch_id' => $lead->batch_id,
                ],
                'student_detail' => $lead->studentDetails
                    ? $this->transformStudentDetail($lead->studentDetails)
                    : null,
                'form_meta' => [
                    'boards' => $boards,
                    'country_codes' => $countryCodes,
                    'course' => $course ? [
                        'id' => $course->id,
                        'title' => $course->title,
                    ] : null,
                    'batch' => $batch ? [
                        'id' => $batch->id,
                        'title' => $batch->title,
                    ] : null,
                    'course_type' => $courseType,
                    'university' => $university ? [
                        'id' => $university->id,
                        'title' => $university->title,
                    ] : null,
                    'amounts' => [
                        'course' => $courseAmount,
                        'batch' => $batchAmount,
                        'extra' => $extraAmount,
                        'university' => $universityAmount,
                        'additional' => $additionalAmount,
                        'total' => $totalAmount,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Convert a lead into a student (mirrors web convert submit).
     */
    public function convertSubmit(Request $request, Lead $lead)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!$this->canViewLead($lead, $user)) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied for this lead.',
            ], 403);
        }

        if ($lead->is_converted) {
            return response()->json([
                'status' => false,
                'message' => 'Lead already converted.',
            ], 409);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'dob' => 'nullable|date|before_or_equal:today',
            'board_id' => 'nullable|exists:boards,id',
            'remarks' => 'nullable|string|max:1000',
            'payment_collected' => 'boolean',
            'payment_amount' => 'required_if:payment_collected,1|required_if:payment_collected,true|required_if:payment_collected,"1"|nullable|numeric|min:0.01',
            'payment_type' => 'required_if:payment_collected,1|required_if:payment_collected,true|required_if:payment_collected,"1"|nullable|in:Cash,Online,Bank,Cheque,Card,Other',
            'transaction_id' => 'nullable|string|max:255',
            'payment_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please correct the errors below.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $leadDetail = LeadDetail::firstOrCreate(
                ['lead_id' => $lead->id],
                ['course_id' => $lead->course_id]
            );

            if ($request->filled('dob')) {
                $leadDetail->update(['date_of_birth' => $request->dob]);
            }

            $dob = $request->dob ?? (($leadDetail && $leadDetail->date_of_birth)
                ? Carbon::parse($leadDetail->date_of_birth)->format('Y-m-d')
                : null);
            $subjectId = $leadDetail ? $leadDetail->subject_id : null;

            $convertedLead = ConvertedLead::create([
                'lead_id' => $lead->id,
                'name' => $request->name,
                'code' => $request->code,
                'phone' => $request->phone,
                'email' => $request->email,
                'dob' => $dob,
                'course_id' => $lead->course_id,
                'batch_id' => $lead->batch_id,
                'board_id' => $request->board_id,
                'subject_id' => $subjectId,
                'candidate_status_id' => 1,
                'remarks' => $request->remarks,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            $lead->update([
                'is_converted' => true,
                'updated_by' => $user->id,
            ]);

            $invoice = null;
            if ($lead->course_id) {
                $invoice = $this->autoGenerateInvoice($convertedLead, $lead->course_id, $user->id);
            }

            if ($request->boolean('payment_collected') && $invoice) {
                $this->autoCreatePayment(
                    $invoice,
                    (float) $request->payment_amount,
                    $request->payment_type,
                    $request->transaction_id,
                    $request->file('payment_file'),
                    $user->id
                );
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Lead converted successfully!',
                'data' => [
                    'converted_lead_id' => $convertedLead->id,
                    'invoice_id' => $invoice?->id,
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while converting the lead. Please try again.',
            ], 500);
        }
    }

    /**
     * Build the base query with eager loaded relationships.
     */
    private function autoGenerateInvoice(ConvertedLead $student, int $courseId, int $userId): ?Invoice
    {
        try {
            $course = Course::find($courseId);
            if (!$course) {
                return null;
            }

            $existingInvoice = Invoice::where('student_id', $student->id)
                ->where('course_id', $courseId)
                ->first();

            if ($existingInvoice) {
                return $existingInvoice;
            }

            $totalAmount = (float) ($course->amount ?? 0);
            $batchId = $student->batch_id ?? optional($student->leadDetail)->batch_id;

            if ($batchId) {
                $batch = Batch::find($batchId);
                if ($batch && $batch->amount) {
                    $totalAmount += (float) $batch->amount;
                }
            }

            if ($courseId == 9 && $student->leadDetail) {
                $courseType = $student->leadDetail->course_type;
                $universityId = $student->leadDetail->university_id;

                if ($courseType && $universityId) {
                    $university = University::find($universityId);
                    if ($university) {
                        if ($courseType === 'UG') {
                            $totalAmount += (float) ($university->ug_amount ?? 0);
                        } elseif ($courseType === 'PG') {
                            $totalAmount += (float) ($university->pg_amount ?? 0);
                        }
                    }
                }
            } elseif ($courseId == 16 && $student->leadDetail && $student->leadDetail->class === 'sslc') {
                $totalAmount += 10000; // ₹10,000 extra for GMVSS SSLC class
            }

            return Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'invoice_type' => 'course',
                'course_id' => $courseId,
                'batch_id' => $batchId,
                'student_id' => $student->id,
                'total_amount' => $totalAmount,
                'invoice_date' => now()->toDateString(),
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Auto-create payment linked to invoice (pending approval).
     */
    private function autoCreatePayment(Invoice $invoice, float $amount, string $paymentType, ?string $transactionId, ?UploadedFile $fileUpload, int $userId): ?Payment
    {
        try {
            $previousBalance = Payment::where('invoice_id', $invoice->id)
                ->where('status', 'Approved')
                ->sum('amount_paid');

            $filePath = null;
            if ($fileUpload) {
                $fileName = time() . '_' . $fileUpload->getClientOriginalName();
                $filePath = $fileUpload->storeAs('payments', $fileName, 'public');
            }

            return Payment::create([
                'invoice_id' => $invoice->id,
                'amount_paid' => $amount,
                'previous_balance' => $previousBalance,
                'payment_type' => $paymentType,
                'transaction_id' => $transactionId,
                'file_upload' => $filePath,
                'status' => 'Pending Approval',
                'created_by' => $userId,
            ]);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Generate invoice number similar to web counterpart.
     */
    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $year = now()->year;
        $month = now()->format('m');

        $lastInvoice = Invoice::where('invoice_number', 'like', $prefix . $year . $month . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        $newNumber = $lastInvoice
            ? ((int) substr($lastInvoice->invoice_number, -4)) + 1
            : 1;

        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

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
                        'sslc_verified_by',
                        'sslc_verified_at',
                        'plustwo_verification_status',
                        'plustwo_verified_by',
                        'plustwo_verified_at',
                        'ug_verification_status',
                        'ug_verified_by',
                        'ug_verified_at',
                        'post_graduation_certificate_verification_status',
                        'post_graduation_certificate_verified_by',
                        'post_graduation_certificate_verified_at',
                        'birth_certificate_verification_status',
                        'birth_certificate_verified_by',
                        'birth_certificate_verified_at',
                        'passport_photo_verification_status',
                        'passport_photo_verified_by',
                        'passport_photo_verified_at',
                        'adhar_front_verification_status',
                        'adhar_front_verified_by',
                        'adhar_front_verified_at',
                        'adhar_back_verification_status',
                        'adhar_back_verified_by',
                        'adhar_back_verified_at',
                        'signature_verification_status',
                        'signature_verified_by',
                        'signature_verified_at',
                        'other_document_verification_status',
                        'other_document_verified_by',
                        'other_document_verified_at',
                        'admin_remarks',
                        'reviewed_by',
                        'reviewed_at',
                    ])->with([
                        'course:id,title',
                        'subject:id,title',
                        'batch:id,title',
                        'subCourse:id,title',
                        'classTime:id,course_id,from_time,to_time',
                        'reviewedBy:id,name',
                        'sslcCertificates:id,lead_detail_id,certificate_path,verification_status,verified_at,verified_by',
                        'sslcCertificates.verifiedBy:id,name',
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
            $registrationStatus = $request->get('registration_status', 'all');

            if (in_array($registrationStatus, ['pending', 'approved', 'rejected'])) {
                $query->whereHas('studentDetails', function ($q) use ($registrationStatus) {
                    $q->where('status', $registrationStatus);
                });
            }
            // If 'all' or not provided, don't filter by status - show all leads
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
        $documentsStatus = $studentDetail ? $studentDetail->getDocumentVerificationStatus() : null;

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
            'documents_status' => $documentsStatus,
            'documents_status_label' => $documentsStatus
                ? $this->formatDocumentStatusLabel($documentsStatus)
                : null,
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
        $documentsStatus = $detail->getDocumentVerificationStatus();

        return [
            'id' => $detail->id,
            'status' => $detail->status,
            'course' => $detail->course ? $detail->course->title : null,
            'subject' => $detail->subject ? $detail->subject->title : null,
            'batch' => $detail->batch ? $detail->batch->title : null,
            'sub_course' => $detail->subCourse ? $detail->subCourse->title : null,
            'class_time' => $detail->classTime ? [
                'title' => $this->formatClassTimeLabel($detail->classTime),
                'start_time' => $detail->classTime->from_time,
                'end_time' => $detail->classTime->to_time,
            ] : null,
            'second_language' => $detail->second_language,
            'passed_year' => $detail->passed_year,
            'programme_type' => $detail->programme_type,
            'student_name' => $detail->student_name,
            'father_name' => $detail->father_name,
            'mother_name' => $detail->mother_name,
            'date_of_birth' => $detail->date_of_birth
                ? Carbon::parse($detail->date_of_birth)->format('Y-m-d')
                : null,
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
            'documents_status' => $documentsStatus,
            'documents_status_label' => $documentsStatus
                ? $this->formatDocumentStatusLabel($documentsStatus)
                : null,
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
            $verifiedAtField = $field . '_verified_at';
            [$uploaded, $status, $verifiedAt] = $this->resolveDocumentSummary($detail, $field, $statusField, $verifiedAtField);

            $summary[$field] = [
                'label' => $label,
                'uploaded' => $uploaded,
                'status' => $status,
                'verified_at' => $verifiedAt,
            ];
        }

        if ($detail->sslcCertificates && $detail->sslcCertificates->count() > 0) {
            $summary['sslc_multiple'] = $detail->sslcCertificates->map(function ($certificate) {
                return [
                    'url' => $this->buildFileUrl($certificate->certificate_path ?? $certificate->file_path ?? null),
                    'status' => $certificate->verification_status ?? 'pending',
                    'verified_by' => $certificate->verifiedBy ? $certificate->verifiedBy->name : null,
                    'verified_at' => $this->formatDateTimeValue($certificate->verified_at),
                ];
            });
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
                'status' => $detail->$statusField ?? (!empty($detail->$field) ? 'pending' : null),
                'verified_by' => $detail->$verifiedByField,
                'verified_at' => $this->formatDateTimeValue($detail->$verifiedAtField),
            ];
        }

        if ($detail->sslcCertificates && $detail->sslcCertificates->count() > 0) {
            $documents['sslc_multiple'] = $detail->sslcCertificates->map(function ($certificate) {
                return [
                    'url' => $this->buildFileUrl($certificate->certificate_path ?? $certificate->file_path ?? null),
                    'status' => $certificate->verification_status,
                    'verified_by' => $certificate->verifiedBy ? $certificate->verifiedBy->name : null,
                    'verified_at' => $this->formatDateTimeValue($certificate->verified_at),
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

        $publicDisk = Storage::disk('public');

        if ($publicDisk->exists($path)) {
            /** @var FilesystemAdapter $publicDisk */
            return $publicDisk->url($path);
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
     * Build a readable class time label.
     */
    private function formatClassTimeLabel(?ClassTime $classTime): ?string
    {
        if (!$classTime) {
            return null;
        }

        $from = $this->formatTimeValue($classTime->from_time);
        $to = $this->formatTimeValue($classTime->to_time);

        if ($from && $to) {
            return "{$from} - {$to}";
        }

        return $from ?? $to;
    }

    /**
     * Format a single time value to h:i A, fallback to raw string.
     */
    private function formatTimeValue(?string $time): ?string
    {
        if (!$time) {
            return null;
        }

        try {
            return Carbon::createFromFormat('H:i:s', $time)->format('h:i A');
        } catch (\Exception $e) {
            try {
                return Carbon::parse($time)->format('h:i A');
            } catch (\Exception $e) {
                return $time;
            }
        }
    }

    /**
     * Format a datetime value to Y-m-d H:i:s.
     */
    private function formatDateTimeValue($value): ?string
    {
        if (!$value) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->format('Y-m-d H:i:s');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Build human friendly label for document status badge.
     */
    private function formatDocumentStatusLabel(string $status): string
    {
        return $status === 'verified' ? 'Documents Verified' : 'Documents Pending';
    }

    /**
     * Resolve upload/status info for document summary.
     */
    private function resolveDocumentSummary(LeadDetail $detail, string $field, string $statusField, string $verifiedAtField): array
    {
        $uploaded = !empty($detail->$field);
        $status = $detail->$statusField ?? null;
        $verifiedAt = $this->formatDateTimeValue($detail->$verifiedAtField);

        if ($uploaded) {
            $status = $status ?? 'pending';
        }

        if ($field === 'sslc_certificate' && $detail->sslcCertificates && $detail->sslcCertificates->count() > 0) {
            $uploaded = true;
            $hasPending = $detail->sslcCertificates->contains(function ($certificate) {
                return ($certificate->verification_status ?? 'pending') !== 'verified';
            });

            $status = $hasPending ? 'pending' : 'verified';

            $firstVerified = $detail->sslcCertificates->firstWhere('verification_status', 'verified');

            if ($firstVerified && $firstVerified->verified_at) {
                $verifiedAt = $this->formatDateTimeValue($firstVerified->verified_at);
            }
        }

        return [$uploaded, $status, $verifiedAt];
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


