<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\ConvertedLead;
use App\Models\Course;
use App\Models\Batch;
use App\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices for a specific student
     */
    public function index(Request $request, $studentId)
    {
        $student = ConvertedLead::with(['course', 'lead'])->findOrFail($studentId);
        
        // Check permissions
        $this->checkStudentAccess($student);
        
        $invoices = Invoice::with(['course', 'batch', 'payments'])
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate summary
        $summary = [
            'total_invoices' => $invoices->count(),
            'total_amount' => $invoices->sum('total_amount'),
            'total_paid' => $invoices->sum('paid_amount'),
            'total_pending' => $invoices->sum('total_amount') - $invoices->sum('paid_amount'),
        ];

        return view('admin.invoices.index', compact('student', 'invoices', 'summary'));
    }

    /**
     * Display the specified invoice
     */
    public function show($id)
    {
        $invoice = Invoice::with(['course', 'batch', 'student.lead', 'payments' => function($query) {
            $query->with('createdBy')->orderBy('created_at', 'desc');
        }])
            ->findOrFail($id);
        
        // Check permissions
        $this->checkStudentAccess($invoice->student);

        // Find the first payment (oldest approved payment) for tax invoice
        $firstPayment = \App\Models\Payment::where('invoice_id', $id)
            ->where('status', 'Approved')
            ->orderBy('created_at', 'asc')
            ->first();

        return view('admin.invoices.show', compact('invoice', 'firstPayment'));
    }

    /**
     * Create a new invoice for a student
     */
    public function create($studentId)
    {
        $student = ConvertedLead::with(['course', 'batch'])->findOrFail($studentId);
        
        // Check permissions
        $this->checkStudentAccess($student);
        
        $courses = Course::where('is_active', true)->get();
        $batches = Batch::where('is_active', true)->get();

        return view('admin.invoices.create', compact('student', 'courses', 'batches'));
    }

    /**
     * Store a newly created invoice
     */
    public function store(Request $request, $studentId)
    {
        Log::info('[InvoiceController@store] Incoming invoice create', [
            'student_id' => $studentId,
            'payload' => $request->all()
        ]);

        // Normalize/override totals per type and guard fields
        if ($request->invoice_type === 'batch_change') {
            $request->merge(['total_amount' => 2000]);
        } elseif ($request->invoice_type === 'e-service' && $request->filled('service_amount')) {
            $request->merge(['total_amount' => $request->service_amount]);
        } elseif ($request->invoice_type === 'fine' && $request->filled('fine_amount')) {
            // Keep total amount in sync with the fine amount
            $request->merge(['total_amount' => $request->fine_amount]);
        } elseif ($request->invoice_type === 'course') {
            // Calculate total amount similar to lead convert form
            $student = ConvertedLead::with('leadDetail')->findOrFail($studentId);
            $calculatedAmount = $this->calculateCourseInvoiceAmount(
                $student,
                $request->course_id,
                $request->batch_id
            );
            // Only override if not manually edited (user can still edit)
            if (!$request->has('total_amount') || $request->total_amount == '') {
                $request->merge(['total_amount' => $calculatedAmount]);
            }
        }

        $validator = Validator::make($request->all(), [
            'invoice_type' => 'required|in:course,e-service,batch_change,fine',
            'course_id' => 'nullable|required_if:invoice_type,course|exists:courses,id',
            'batch_id' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    // Batch is required for course type unless course_id is 23 (EduMaster)
                    if ($request->invoice_type === 'course' && $request->course_id != 23 && empty($value)) {
                        $fail('The batch field is required when course type is selected (except for EduMaster).');
                    }
                },
                'exists:batches,id'
            ],
            'service_name' => 'nullable|required_if:invoice_type,e-service|string|max:255',
            'service_amount' => 'nullable|required_if:invoice_type,e-service|numeric|min:0',
            'fine_type' => 'nullable|required_if:invoice_type,fine|string|max:255',
            'fine_amount' => 'nullable|required_if:invoice_type,fine|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'invoice_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            Log::warning('[InvoiceController@store] Validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $student = ConvertedLead::findOrFail($studentId);
            
            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber();
            
            $invoiceData = [
                'invoice_number' => $invoiceNumber,
                'invoice_type' => $request->invoice_type,
                'student_id' => $studentId,
                'total_amount' => $request->total_amount,
                'invoice_date' => $request->invoice_date,
                'created_by' => AuthHelper::getCurrentUserId(),
                'updated_by' => AuthHelper::getCurrentUserId(),
            ];

            // Add type-specific fields
            if ($request->invoice_type === 'course') {
                $invoiceData['course_id'] = $request->course_id;
                if ($request->filled('batch_id')) {
                    $invoiceData['batch_id'] = $request->batch_id;
                }
            } elseif ($request->invoice_type === 'batch_change') {
                $invoiceData['batch_id'] = $request->batch_id;
                $invoiceData['total_amount'] = 2000; // Fixed amount for batch change
            } elseif ($request->invoice_type === 'e-service') {
                $invoiceData['service_name'] = $request->service_name;
                $invoiceData['service_amount'] = $request->service_amount;
            } elseif ($request->invoice_type === 'fine') {
                // Reuse service fields to store fine metadata without schema changes
                $invoiceData['service_name'] = $request->fine_type;
                $invoiceData['service_amount'] = $request->fine_amount;
                $invoiceData['total_amount'] = $request->fine_amount;
            }
            
            $invoice = Invoice::create($invoiceData);

            // Handle batch transfer for batch_change invoices
            if ($request->invoice_type === 'batch_change' && $request->batch_id) {
                $this->transferStudentBatch($studentId, $request->batch_id);
            }

            Log::info('[InvoiceController@store] Invoice created', [
                'invoice_id' => $invoice->id,
                'invoice_type' => $invoice->invoice_type
            ]);

            return redirect()->route('admin.invoices.show', $invoice->id)
                ->with('message_success', 'Invoice created successfully!');

        } catch (\Exception $e) {
            Log::error('[InvoiceController@store] Invoice create failed: ' . $e->getMessage(), [
                'payload' => $request->all()
            ]);
            return redirect()->back()
                ->with('message_danger', 'An error occurred while creating the invoice. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show modal to edit invoice amount
     */
    public function editAmount($invoiceId)
    {
        $invoice = Invoice::with(['student', 'batch', 'course'])->findOrFail($invoiceId);
        $this->checkStudentAccess($invoice->student);

        return view('admin.invoices.edit-amount-modal', compact('invoice'));
    }

    /**
     * Update invoice amount
     */
    public function updateAmount(Request $request, $invoiceId)
    {
        $invoice = Invoice::with('student')->findOrFail($invoiceId);
        $this->checkStudentAccess($invoice->student);

        $request->validate([
            'total_amount' => 'required|numeric|min:' . $invoice->pending_amount,
        ], [
            'total_amount.min' => 'New amount cannot be less than the pending amount (₹' . number_format($invoice->pending_amount, 2) . ').',
        ]);

        $invoice->total_amount = $request->total_amount;

        if ($invoice->paid_amount >= $invoice->total_amount) {
            $invoice->status = 'Fully Paid';
        } elseif ($invoice->paid_amount > 0) {
            $invoice->status = 'Partially Paid';
        } else {
            $invoice->status = 'Not Paid';
        }

        $invoice->updated_by = AuthHelper::getCurrentUserId();
        $invoice->save();

        return redirect()
            ->route('admin.invoices.index', $invoice->student_id)
            ->with('message_success', 'Invoice amount updated successfully.');
    }

    /**
     * Auto-generate invoice when converting a lead
     */
    public function autoGenerate($studentId, $courseId, $customTotalAmount = null, $feeBreakdown = null)
    {
        try {
            $student = ConvertedLead::findOrFail($studentId);
            $course = Course::findOrFail($courseId);
            
            // Check if invoice already exists for this student and course
            $existingInvoice = Invoice::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->first();
                
            if ($existingInvoice) {
                return $existingInvoice;
            }
            
            // Get batch_id
            $batchId = $student->batch_id ?? optional($student->leadDetail)->batch_id;
            
            $feePgAmount = null;
            $feeUgAmount = null;
            $feePlustwoAmount = null;
            $feeSslcAmount = null;

            // For course_id 23, store fee breakdown and total (custom or derived)
            if ($courseId == 23) {
                if (is_array($feeBreakdown)) {
                    $feePgAmount = isset($feeBreakdown['fee_pg_amount']) ? (float) $feeBreakdown['fee_pg_amount'] : null;
                    $feeUgAmount = isset($feeBreakdown['fee_ug_amount']) ? (float) $feeBreakdown['fee_ug_amount'] : null;
                    $feePlustwoAmount = isset($feeBreakdown['fee_plustwo_amount']) ? (float) $feeBreakdown['fee_plustwo_amount'] : null;
                    $feeSslcAmount = isset($feeBreakdown['fee_sslc_amount']) ? (float) $feeBreakdown['fee_sslc_amount'] : null;
                }

                if ($customTotalAmount !== null) {
                    $totalAmount = (float) $customTotalAmount;
                } else {
                    $totalAmount = (float) (($feePgAmount ?? 0) + ($feeUgAmount ?? 0) + ($feePlustwoAmount ?? 0) + ($feeSslcAmount ?? 0));
                }
            } else {
                // Calculate total amount
                $totalAmount = (float) ($course->amount ?? 0);
                $batchAmount = 0.0;

                // Determine batch and add batch amount if available
                if ($batchId) {
                    $batch = Batch::find($batchId);
                    if ($batch) {
                        // If B2B student, prefer batch B2B pricing (fallback to normal rules if not set)
                        if ((int) ($student->is_b2b ?? 0) === 1 && !is_null($batch->b2b_amount)) {
                            $batchAmount = (float) $batch->b2b_amount;
                        } else {
                            if ($courseId == 16) {
                                $studentClass = optional($student->leadDetail)->class;
                                $normalizedClass = $studentClass ? strtolower($studentClass) : null;

                                if ($normalizedClass === 'sslc' && !is_null($batch->sslc_amount)) {
                                    $batchAmount = (float) $batch->sslc_amount;
                                } elseif (!is_null($batch->plustwo_amount)) {
                                    $batchAmount = (float) $batch->plustwo_amount;
                                } elseif ($batch->amount) {
                                    $batchAmount = (float) $batch->amount;
                                }
                            } elseif ($batch->amount) {
                                $batchAmount = (float) $batch->amount;
                            }
                        }
                    }
                }
                $totalAmount += $batchAmount;
                
                // Add university amount for UG/PG course (course_id = 9)
                if ($courseId == 9 && $student->leadDetail) {
                    $courseType = $student->leadDetail->course_type;
                    $universityId = $student->leadDetail->university_id;
                    
                    if ($universityId && $courseType) {
                        $university = \App\Models\University::find($universityId);
                        if ($university) {
                            if ($courseType === 'UG') {
                                $totalAmount += $university->ug_amount ?? 0;
                            } elseif ($courseType === 'PG') {
                                $totalAmount += $university->pg_amount ?? 0;
                            }
                        }
                    }
                }
            }
            
            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber();
            
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'invoice_type' => 'course',
                'course_id' => $courseId,
                'batch_id' => $batchId,
                'student_id' => $studentId,
                'total_amount' => $totalAmount,
                'fee_pg_amount' => $feePgAmount,
                'fee_ug_amount' => $feeUgAmount,
                'fee_plustwo_amount' => $feePlustwoAmount,
                'fee_sslc_amount' => $feeSslcAmount,
                'invoice_date' => now()->toDateString(),
                'created_by' => AuthHelper::getCurrentUserId(),
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            return $invoice;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $year = now()->year;
        $month = now()->format('m');
        
        // Get the last invoice number for this month
        $lastInvoice = Invoice::where('invoice_number', 'like', $prefix . $year . $month . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();
        
        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if user has access to the student
     */
    private function checkStudentAccess($student)
    {
        $currentUserId = AuthHelper::getCurrentUserId();
        $currentUserRole = AuthHelper::getCurrentUserRole();
        
        // Senior Manager: Can access all students
        if (\App\Helpers\RoleHelper::is_senior_manager()) {
            return;
        }
        
        // General Manager: Can access all students
        if (\App\Helpers\RoleHelper::is_general_manager()) {
            return;
        }
        
        // Team Lead: Can access students from their team
        if (\App\Helpers\RoleHelper::is_team_lead()) {
            $teamMemberIds = \App\Models\User::where('team_id', AuthHelper::getCurrentUserTeam())
                ->pluck('id')
                ->toArray();
                
            if (!in_array($student->created_by, $teamMemberIds)) {
                abort(403, 'Access denied. You can only view students from your team.');
            }
            return;
        }
        
        switch ($currentUserRole) {
            case 1: // Super Admin
            case 2: // Admin
            case 11: // General Manager
            case 4: // Admission Counsellor
            case 6: // Finance
            case 7: // Post Sales
                // Can access all students
                break;
                
            case 3: // Telecaller
                // Can only access students they created
                if ($student->created_by != $currentUserId) {
                    abort(403, 'Access denied. You can only view students you created.');
                }
                break;
                
            case 5: // Academic Assistant
                // Can only access students assigned to them
                if ($student->academic_assistant_id != $currentUserId) {
                    abort(403, 'Access denied. You can only view students assigned to you.');
                }
                break;
                
            default:
                abort(403, 'Access denied.');
        }
    }

    /**
     * Transfer student to a new batch
     */
    private function transferStudentBatch($studentId, $batchId)
    {
        try {
            DB::beginTransaction();

            // Update converted_leads table
            $convertedLead = ConvertedLead::findOrFail($studentId);
            $convertedLead->update(['batch_id' => $batchId]);

            // Update lead_details table via the lead relationship
            if ($convertedLead->lead && $convertedLead->lead->leadDetails) {
                $convertedLead->lead->leadDetails->update(['batch_id' => $batchId]);
            }

            DB::commit();
            
            Log::info('Student batch transferred successfully', [
                'student_id' => $studentId,
                'new_batch_id' => $batchId
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to transfer student batch: ' . $e->getMessage(), [
                'student_id' => $studentId,
                'batch_id' => $batchId
            ]);
            throw $e;
        }
    }

    /**
     * Calculate course invoice amount similar to lead convert form
     */
    private function calculateCourseInvoiceAmount(ConvertedLead $student, ?int $courseId, ?int $batchId = null): float
    {
        $course = $courseId ? Course::find($courseId) : null;
        $batch = $batchId ? Batch::find($batchId) : null;

        $courseAmount = $course ? (float) ($course->amount ?? 0) : 0.0;
        $batchAmount = 0.0;
        $universityAmount = 0.0;

        $leadDetail = $student->leadDetail;
        if (!$leadDetail && $student->lead_id) {
            $leadDetail = \App\Models\LeadDetail::where('lead_id', $student->lead_id)->first();
        }

        // Determine batch amount (B2B pricing overrides; else class-specific pricing for GMVSS (course 16))
        if ($batch) {
            if ((int) ($student->is_b2b ?? 0) === 1 && !is_null($batch->b2b_amount)) {
                $batchAmount = (float) $batch->b2b_amount;
            } elseif ($course && (int) $course->id === 16 && $leadDetail) {
                $studentClass = strtolower($leadDetail->class ?? '');
                if ($studentClass === 'sslc' && !is_null($batch->sslc_amount)) {
                    $batchAmount = (float) $batch->sslc_amount;
                } elseif (!is_null($batch->plustwo_amount)) {
                    $batchAmount = (float) $batch->plustwo_amount;
                } else {
                    $batchAmount = (float) ($batch->amount ?? 0);
                }
            } else {
                $batchAmount = (float) ($batch->amount ?? 0);
            }
        }

        // Add university amount for UG/PG course (course_id = 9)
        if ($course && (int) $course->id === 9 && $leadDetail) {
            $courseType = $leadDetail->course_type;
            $universityId = $leadDetail->university_id;
            if ($universityId) {
                $university = \App\Models\University::find($universityId);
                if ($university) {
                    if ($courseType === 'UG') {
                        $universityAmount += (float) ($university->ug_amount ?? 0);
                    } elseif ($courseType === 'PG') {
                        $universityAmount += (float) ($university->pg_amount ?? 0);
                    }
                }
            }
        }

        $totalAmount = $courseAmount + $batchAmount + $universityAmount;

        return $totalAmount;
    }

    /**
     * Calculate total amount for invoice (API endpoint)
     */
    public function calculateAmount(Request $request, $studentId)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'batch_id' => 'nullable|exists:batches,id',
        ]);

        $student = ConvertedLead::with('leadDetail')->findOrFail($studentId);
        $totalAmount = $this->calculateCourseInvoiceAmount(
            $student,
            $request->course_id,
            $request->batch_id
        );

        return response()->json([
            'success' => true,
            'total_amount' => $totalAmount
        ]);
    }
}
