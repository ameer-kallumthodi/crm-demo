<?php

namespace App\Http\Controllers;

use App\Helpers\RoleHelper;
use App\Helpers\AuthHelper;
use App\Models\ConvertedLead;
use App\Models\ConvertedStudentActivity;
use App\Models\Course;
use App\Models\LeadActivity;
use App\Models\User;
use App\Services\LeadCallLogService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Mpdf\Mpdf;

class PostSalesConvertedLeadController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureAccess();

        $courses = Course::where('is_active', 1)->orderBy('title')->get(['id', 'title']);
        $telecallers = User::select('id', 'name')->nonMarketingTelecallers()->where('is_active', true)->orderBy('name')->get();

        return view('admin.post-sales.converted-leads.index', compact('courses', 'telecallers'));
    }

    /**
     * AJAX endpoint for DataTables to fetch converted students data
     */
    public function getPostSalesConvertedStudentsData(Request $request): JsonResponse
    {
        try {
            $this->ensureAccess();
            
            set_time_limit(config('timeout.max_execution_time', 300));

            // Build the query
            $query = ConvertedLead::with([
                'course',
                'batch',
                'admissionBatch',
                'subject',
                'lead.telecaller:id,name'
            ]);

            // Apply filters
            if ($request->filled('search') && is_array($request->search) && isset($request->search['value']) && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where(function($q) use ($searchValue) {
                    $q->where('name', 'LIKE', "%{$searchValue}%")
                      ->orWhere('email', 'LIKE', "%{$searchValue}%")
                      ->orWhere('phone', 'LIKE', "%{$searchValue}%")
                      ->orWhere('register_number', 'LIKE', "%{$searchValue}%");
                });
            } elseif ($request->filled('search') && !is_array($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('register_number', 'like', "%{$search}%");
                });
            }

            if ($request->filled('course_id')) {
                $query->where('course_id', $request->course_id);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            if ($request->filled('telecaller_id')) {
                $query->whereHas('lead', function($q) use ($request) {
                    $q->where('telecaller_id', $request->telecaller_id);
                });
            }

            // Get total count before filtering
            $totalRecords = ConvertedLead::count();

            // Get filtered count
            $filteredCount = $query->count();

            // Column mapping for ordering
            $columns = [
                0 => 'id', // Index column
                1 => 'name', // Name
                2 => 'phone', // Phone
                3 => 'email', // Email
                4 => 'id', // BDE Name - no sorting
                5 => 'created_at', // Converted Date
                6 => 'course_id', // Course
                7 => 'id', // Batch - no sorting
                8 => 'id', // Admission Batch - no sorting
                9 => 'id', // Subject - no sorting
                10 => 'id', // Status - no sorting
                11 => 'id', // Paid Status - no sorting
                12 => 'id', // Call Status - no sorting
                13 => 'id', // Called Date - no sorting
                14 => 'id', // Call Time - no sorting
                15 => 'id', // Post Sale Followup - no sorting
                16 => 'id', // Remark - no sorting
                17 => 'id', // Actions - no sorting
            ];

            // Apply ordering
            $order = $request->get('order', []);
            $orderColumn = isset($order[0]['column']) ? (int)$order[0]['column'] : 5; // Default to created_at
            $orderDir = isset($order[0]['dir']) ? $order[0]['dir'] : 'desc';

            $orderColumnName = $columns[$orderColumn] ?? 'created_at';
            if ($orderColumnName !== 'id') {
                $query->orderBy($orderColumnName, $orderDir);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Apply pagination
            $start = $request->get('start', 0);
            $length = $request->get('length', 25);
            $convertedLeads = $query->skip($start)->take($length)->get();

            // Format data for DataTables
            $data = [];
            foreach ($convertedLeads as $index => $convertedLead) {
                $row = [
                    'DT_RowId' => 'converted_lead_' . $convertedLead->id,
                    'DT_RowData' => ['id' => $convertedLead->id],
                    'index' => $start + $index + 1,
                    'name' => $this->renderName($convertedLead),
                    'phone' => \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone),
                    'email' => $convertedLead->email ?? 'N/A',
                    'bde_name' => $convertedLead->lead?->telecaller?->name ?? 'Unassigned',
                    'created_at' => $convertedLead->created_at ? $convertedLead->created_at->format('d M Y h:i A') : 'N/A',
                    'course' => $convertedLead->course?->title ?? 'N/A',
                    'batch' => $convertedLead->batch?->title ?? 'N/A',
                    'admission_batch' => $convertedLead->admissionBatch?->title ?? 'N/A',
                    'subject' => $convertedLead->subject?->title ?? 'N/A',
            'status' => $this->renderStatus($convertedLead),
                    'paid_status' => $this->renderPaidStatus($convertedLead),
                    'call_status' => $this->renderCallStatus($convertedLead),
                    'called_date' => $this->renderCalledDate($convertedLead),
                    'called_time' => $this->renderCalledTime($convertedLead),
                    'postsale_followup' => $this->renderPostsaleFollowup($convertedLead),
                    'post_sales_remarks' => $this->renderPostSalesRemarks($convertedLead),
            'actions' => $this->renderActions($convertedLead),
            'DT_RowClass' => $this->getRowClass($convertedLead),
                    // Mobile view data
                    'mobile_view' => $this->renderMobileView($convertedLead)
                ];

                $data[] = $row;
            }

            // Build response array
            $responseData = [
                'draw' => intval($request->get('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredCount,
                'data' => $data
            ];

            return response()->json($responseData);

        } catch (\Exception $e) {
            Log::error('Error fetching post-sales converted students data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'draw' => intval($request->get('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'An error occurred while fetching data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Render name column HTML
     */
    private function renderName($convertedLead)
    {
        $name = $convertedLead->name ?? '';
        $registerNumber = $convertedLead->register_number ?? 'No register #';
        $firstChar = mb_substr($name, 0, 1, 'UTF-8');
        
        $html = '<div class="d-flex align-items-center">';
        $html .= '<div class="avtar avtar-s rounded-circle bg-light-primary me-2 d-flex align-items-center justify-content-center">';
        $html .= '<span class="f-16 fw-bold text-primary">' . htmlspecialchars(strtoupper($firstChar), ENT_QUOTES, 'UTF-8') . '</span>';
        $html .= '</div>';
        $html .= '<div>';
        $html .= '<div class="fw-semibold">' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</div>';
        $html .= '<small class="text-muted">' . htmlspecialchars($registerNumber, ENT_QUOTES, 'UTF-8') . '</small>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * Render status column HTML
     */
    private function renderStatus($convertedLead)
    {
        $status = $convertedLead->status ?? 'N/A';
        $badgeClass = match($status) {
            'paid' => 'bg-success',
            'unpaid' => 'bg-warning',
            'cancel' => 'bg-danger',
            'pending' => 'bg-info',
            'postpond' => 'bg-dark',
            'followup' => 'bg-primary',
            default => 'bg-secondary'
        };
        return '<span class="badge ' . $badgeClass . '">' . htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8') . '</span>';
    }

    /**
     * Render paid status column HTML
     */
    private function renderPaidStatus($convertedLead)
    {
        $paidStatus = $convertedLead->paid_status ?? 'N/A';
        if ($paidStatus === 'N/A') {
            return '<span class="text-muted">N/A</span>';
        }
        return '<span class="badge bg-info">' . htmlspecialchars($paidStatus, ENT_QUOTES, 'UTF-8') . '</span>';
    }

    /**
     * Render call status column HTML
     */
    private function renderCallStatus($convertedLead)
    {
        $callStatus = $convertedLead->call_status ?? 'N/A';
        if ($callStatus === 'N/A') {
            return '<span class="text-muted">N/A</span>';
        }
        $badgeClass = match($callStatus) {
            'Attended' => 'bg-success',
            'Whatsapp connected' => 'bg-success',
            'RNR' => 'bg-warning',
            'Switch off' => 'bg-danger',
            default => 'bg-secondary'
        };
        return '<span class="badge ' . $badgeClass . '">' . htmlspecialchars($callStatus, ENT_QUOTES, 'UTF-8') . '</span>';
    }

    /**
     * Render called date column HTML
     */
    private function renderCalledDate($convertedLead)
    {
        if (!$convertedLead->called_date) {
            return '<span class="text-muted">N/A</span>';
        }
        
        return '<span class="fw-semibold">' . htmlspecialchars($convertedLead->called_date->format('d M Y'), ENT_QUOTES, 'UTF-8') . '</span>';
    }

    /**
     * Render called time column HTML
     */
    private function renderCalledTime($convertedLead)
    {
        if (!$convertedLead->called_time) {
            return '<span class="text-muted">N/A</span>';
        }

        return '<span class="fw-semibold">' . htmlspecialchars($convertedLead->called_time->format('h:i A'), ENT_QUOTES, 'UTF-8') . '</span>';
    }

    /**
     * Render postsale followup column HTML
     */
    private function renderPostsaleFollowup($convertedLead)
    {
        if (!$convertedLead->postsale_followupdate) {
            return '<span class="text-muted">N/A</span>';
        }
        
        $date = $convertedLead->postsale_followupdate->format('d M Y');
        $time = $convertedLead->postsale_followuptime ? date('h:i A', strtotime($convertedLead->postsale_followuptime)) : '';
        
        $html = '<div>';
        $html .= '<div class="fw-semibold">' . htmlspecialchars($date, ENT_QUOTES, 'UTF-8') . '</div>';
        if ($time) {
            $html .= '<small class="text-muted">' . htmlspecialchars($time, ENT_QUOTES, 'UTF-8') . '</small>';
        }
        $html .= '</div>';
        return $html;
    }

    /**
     * Render post sales remarks column HTML
     */
    private function renderPostSalesRemarks($convertedLead)
    {
        $remarks = $convertedLead->post_sales_remarks ?? '';
        if (empty($remarks)) {
            return '<span class="text-muted">N/A</span>';
        }
        // Truncate long remarks for table display
        $truncated = mb_strlen($remarks) > 50 ? mb_substr($remarks, 0, 50) . '...' : $remarks;
        return '<span title="' . htmlspecialchars($remarks, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($truncated, ENT_QUOTES, 'UTF-8') . '</span>';
    }

    /**
     * Render actions column HTML
     */
    private function renderActions($convertedLead)
    {
        $html = '<div class="text-center d-flex gap-1 justify-content-center">';
        $html .= '<a href="' . route('admin.post-sales.converted-leads.show', $convertedLead->id) . '" class="btn btn-sm btn-outline-primary" title="View Details">';
        $html .= '<i class="ti ti-eye"></i>';
        $html .= '</a>';
        $html .= '<a href="' . route('admin.invoices.index', $convertedLead->id) . '" class="btn btn-sm btn-success" title="View Invoice">';
        $html .= '<i class="ti ti-receipt"></i>';
        $html .= '</a>';
        $html .= '<button type="button" class="btn btn-sm btn-outline-success" title="Status Update" onclick="show_ajax_modal(\'' . route('admin.post-sales.converted-leads.status-update', $convertedLead->id) . '\', \'Status Update\')">';
        $html .= '<i class="ti ti-edit"></i>';
        $html .= '</button>';
        if (strcasecmp($convertedLead->status ?? '', 'cancel') === 0) {
            $cancelBtnClass = $convertedLead->is_cancelled ? 'btn-danger' : 'btn-outline-danger';
            $cancelBtnTitle = $convertedLead->is_cancelled ? 'Update cancellation confirmation' : 'Confirm cancellation';
            $html .= '<button type="button" class="btn btn-sm ' . $cancelBtnClass . '" title="' . $cancelBtnTitle . '" onclick="show_ajax_modal(\'' . route('admin.post-sales.converted-leads.cancel-flag', $convertedLead->id) . '\', \'Cancellation Confirmation\')">';
            $html .= '<i class="ti ti-ban"></i>';
            $html .= '</button>';
        }
        $html .= '</div>';
        return $html;
    }

    private function getRowClass($convertedLead): string
    {
        return strcasecmp($convertedLead->status ?? '', 'cancel') === 0 ? 'table-danger cancelled-row' : '';
    }

    /**
     * Render mobile view data
     */
    private function renderMobileView($convertedLead)
    {
        $data = [
            'id' => $convertedLead->id,
            'name' => $convertedLead->name ?? '',
            'register_number' => $convertedLead->register_number ?? 'No register #',
            'status' => $convertedLead->status ?? null,
            'is_cancelled' => (bool) $convertedLead->is_cancelled,
            'phone' => \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone),
            'email' => $convertedLead->email ?? 'N/A',
            'bde_name' => $convertedLead->lead?->telecaller?->name ?? 'Unassigned',
            'created_at' => $convertedLead->created_at ? $convertedLead->created_at->format('d M Y h:i A') : 'N/A',
            'course' => $convertedLead->course?->title ?? 'N/A',
            'batch' => $convertedLead->batch?->title ?? 'N/A',
            'admission_batch' => $convertedLead->admissionBatch?->title ?? 'N/A',
            'subject' => $convertedLead->subject?->title ?? 'N/A',
            'called_date' => $convertedLead->called_date ? $convertedLead->called_date->format('d M Y') : null,
            'called_time' => $convertedLead->called_time ? $convertedLead->called_time->format('h:i A') : null,
            'routes' => [
                'view' => route('admin.post-sales.converted-leads.show', $convertedLead->id),
                'status_update' => route('admin.post-sales.converted-leads.status-update', $convertedLead->id),
                'invoice' => route('admin.invoices.index', $convertedLead->id),
                'cancel_flag' => route('admin.post-sales.converted-leads.cancel-flag', $convertedLead->id),
            ]
        ];
        
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function show($id)
    {
        $this->ensureAccess();

        $convertedLead = ConvertedLead::with([
            'lead',
            'lead.telecaller:id,name',
            'leadDetail.sslcCertificates.verifiedBy',
            'leadDetail.sslcVerifiedBy',
            'leadDetail.plustwoVerifiedBy',
            'leadDetail.ugVerifiedBy',
            'leadDetail.passportPhotoVerifiedBy',
            'leadDetail.adharFrontVerifiedBy',
            'leadDetail.adharBackVerifiedBy',
            'leadDetail.signatureVerifiedBy',
            'leadDetail.birthCertificateVerifiedBy',
            'leadDetail.otherDocumentVerifiedBy',
            'course',
            'batch',
            'admissionBatch',
            'subject',
            'academicAssistant',
            'createdBy',
            'studentDetails.registrationLink'
        ])->findOrFail($id);

        $leadActivities = LeadActivity::where('lead_id', $convertedLead->lead_id)
            ->select('id', 'lead_id', 'reason', 'created_at', 'activity_type', 'description', 'remarks', 'rating', 'followup_date', 'created_by', 'lead_status_id')
            ->with(['leadStatus:id,title', 'createdBy:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        $convertedStudentActivities = ConvertedStudentActivity::where('converted_lead_id', $convertedLead->id)
            ->with(['createdBy:id,name'])
            ->orderBy('activity_date', 'desc')
            ->orderBy('activity_time', 'desc')
            ->get();

        $callLogs = LeadCallLogService::forConvertedLead($convertedLead);
        $listRoute = route('admin.post-sales.converted-leads.index');
        $pdfRoute = route('admin.post-sales.converted-leads.details-pdf', $convertedLead->id);

        return view('admin.converted-leads.show', compact('convertedLead', 'leadActivities', 'convertedStudentActivities', 'callLogs', 'listRoute', 'pdfRoute'));
    }

    /**
     * Show status update modal
     */
    public function statusUpdate($id)
    {
        $this->ensureAccess();

        $convertedLead = ConvertedLead::findOrFail($id);

        return view('admin.post-sales.converted-leads.status-update-modal', compact('convertedLead'));
    }

    /**
     * Handle status update form submission
     */
    public function statusUpdateSubmit(Request $request, $id)
    {
        $this->ensureAccess();

        try {
            $convertedLead = ConvertedLead::findOrFail($id);

            // Validate request
            $validated = $request->validate([
                'status' => 'required|in:paid,unpaid,cancel,postpond,followup',
                'paid_status' => 'nullable|in:Fully paid,Registration Paid,Registration Partially paid,Certificate Paid,Certificate Partially paid,Exam Paid,Exam Fees Partially paid,Halticket Paid,Halticket Partially paid',
                'call_status' => ['required', Rule::in(['RNR', 'Switch off', 'Attended', 'Whatsapp connected'])],
                'called_date' => 'required|date',
                'called_time' => 'required|date_format:H:i',
                'followup_date' => 'nullable|date',
                'post_sales_remarks' => 'nullable|string|max:2000',
            ]);

            // Additional validation: paid_status required when status is 'paid'
            if ($request->status === 'paid' && !$request->paid_status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paid status is required when status is paid.'
                ], 422);
            }

            // Additional validation: followup_date not required when paid_status is 'Fully paid'
            $isFullyPaid = $request->paid_status === 'Fully paid';
            if (
                !$isFullyPaid &&
                !in_array($request->status, ['postpond', 'cancel'], true) &&
                !$request->followup_date
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Followup date is required.'
                ], 422);
            }

            DB::beginTransaction();

            // Update converted lead
            $convertedLead->status = $request->status;
            $convertedLead->paid_status = $request->paid_status;
            $convertedLead->call_status = $request->call_status;
            $convertedLead->called_date = $request->called_date;
            $convertedLead->called_time = $request->called_time;
            $convertedLead->post_sales_remarks = $request->post_sales_remarks;
            
            // Only set followup date/time if not fully paid
            if (!$isFullyPaid && !in_array($request->status, ['postpond', 'cancel'], true)) {
                $convertedLead->postsale_followupdate = $request->followup_date;
            } else {
                $convertedLead->postsale_followupdate = null;
            }
            $convertedLead->postsale_followuptime = null;
            
            $convertedLead->updated_by = AuthHelper::getCurrentUserId();
            $convertedLead->save();

            // Create activity record
            $activity = new ConvertedStudentActivity();
            $activity->converted_lead_id = $convertedLead->id;
            $activity->status = $request->status;
            $activity->paid_status = $request->paid_status;
            $activity->call_status = $request->call_status;
            $activity->called_date = $request->called_date;
            $activity->called_time = $request->called_time;
            $activity->activity_type = 'status_update';
            $activity->description = 'Post Sales Status updated to: ' . $request->status;
            $activity->remark = $request->post_sales_remarks;
            $activity->activity_date = now()->toDateString();
            $activity->activity_time = now()->toTimeString();
            
            // Only set followup date/time if not fully paid
            if (!$isFullyPaid && !in_array($request->status, ['postpond', 'cancel'], true)) {
                $activity->followup_date = $request->followup_date;
                $activity->followup_time = null;
            } else {
                $activity->followup_date = null;
                $activity->followup_time = null;
            }
            
            $activity->created_by = AuthHelper::getCurrentUserId();
            $activity->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating post-sales converted lead status: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show cancellation confirmation modal.
     */
    public function cancelFlag($id)
    {
        $this->ensureAccess();

        $convertedLead = ConvertedLead::findOrFail($id);

        if (strcasecmp($convertedLead->status ?? '', 'cancel') !== 0) {
            abort(404, 'Cancellation confirmation is only available for cancelled leads.');
        }

        return view('admin.post-sales.converted-leads.cancel-flag-modal', compact('convertedLead'));
    }

    /**
     * Update is_cancelled flag for a converted lead.
     */
    public function cancelFlagSubmit(Request $request, $id)
    {
        $this->ensureAccess();

        $convertedLead = ConvertedLead::findOrFail($id);

        if (strcasecmp($convertedLead->status ?? '', 'cancel') !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'Only leads with status cancel can update this flag.'
            ], 422);
        }

        $validated = $request->validate([
            'is_cancelled' => 'required|boolean',
        ]);

        $convertedLead->is_cancelled = (bool) $validated['is_cancelled'];
        $convertedLead->updated_by = AuthHelper::getCurrentUserId();
        $convertedLead->save();

        $message = $convertedLead->is_cancelled
            ? 'Cancellation flagged successfully.'
            : 'Cancellation flag removed.';

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Generate PDF of converted lead details
     */
    public function generateDetailsPdf($id)
    {
        $this->ensureAccess();

        $convertedLead = ConvertedLead::with([
            'lead',
            'leadDetail',
            'course',
            'batch',
            'admissionBatch',
            'subject',
            'academicAssistant',
            'createdBy',
            'studentDetails'
        ])->findOrFail($id);

        // Lead activities
        $leadActivities = LeadActivity::where('lead_id', $convertedLead->lead_id)
            ->select('id', 'lead_id', 'reason', 'created_at', 'activity_type', 'description', 'remarks', 'rating', 'followup_date', 'created_by', 'lead_status_id')
            ->with(['leadStatus:id,title', 'createdBy:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Converted student activities
        $convertedStudentActivities = ConvertedStudentActivity::where('converted_lead_id', $convertedLead->id)
            ->with(['createdBy:id,name'])
            ->orderBy('activity_date', 'desc')
            ->orderBy('activity_time', 'desc')
            ->get();

        $html = view('admin.converted-leads.pdf', compact('convertedLead', 'leadActivities', 'convertedStudentActivities'))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 12,
            'margin_bottom' => 12,
            'margin_left' => 12,
            'margin_right' => 12,
        ]);

        $mpdf->SetTitle('Post Sales Converted Lead Details - #' . $convertedLead->id);
        $mpdf->WriteHTML($html);

        $filename = 'post-sales-converted-lead-details-' . $convertedLead->id . '.pdf';
        return response($mpdf->Output($filename, 'I'))
            ->header('Content-Type', 'application/pdf');
    }

    protected function ensureAccess(): void
    {
        if (
            RoleHelper::is_post_sales() ||
            RoleHelper::is_admin_or_super_admin() ||
            RoleHelper::is_general_manager()
        ) {
            return;
        }

        abort(403, 'Access denied.');
    }
}

