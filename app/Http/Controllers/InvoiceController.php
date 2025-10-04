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
        
        $invoices = Invoice::with(['course', 'payments'])
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
        $invoice = Invoice::with(['course', 'student.lead', 'payments' => function($query) {
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
        $student = ConvertedLead::with(['course'])->findOrFail($studentId);
        
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
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'total_amount' => 'required|numeric|min:0',
            'invoice_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $student = ConvertedLead::findOrFail($studentId);
            $course = Course::findOrFail($request->course_id);
            
            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber();
            
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'course_id' => $request->course_id,
                'student_id' => $studentId,
                'total_amount' => $request->total_amount,
                'invoice_date' => $request->invoice_date,
                'created_by' => AuthHelper::getCurrentUserId(),
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            return redirect()->route('admin.invoices.show', $invoice->id)
                ->with('message_success', 'Invoice created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message_danger', 'An error occurred while creating the invoice. Please try again.')
                ->withInput();
        }
    }

    /**
     * Auto-generate invoice when converting a lead
     */
    public function autoGenerate($studentId, $courseId)
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
            
            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber();
            
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'course_id' => $courseId,
                'student_id' => $studentId,
                'total_amount' => $course->amount,
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
        
        switch ($currentUserRole) {
            case 1: // Super Admin
            case 2: // Admin
            case 4: // Admission Counsellor
            case 6: // Finance
            case 7: // Post Sales
                // Can access all students
                break;
                
            case 3: // Team Lead
                // Can access students from their team
                $teamMemberIds = \App\Models\User::where('team_id', AuthHelper::getCurrentUserTeam())
                    ->pluck('id')
                    ->toArray();
                    
                if (!in_array($student->created_by, $teamMemberIds)) {
                    abort(403, 'Access denied. You can only view students from your team.');
                }
                break;
                
            case 5: // Academic Assistant
                // Can only access students assigned to them
                if ($student->academic_assistant_id != $currentUserId) {
                    abort(403, 'Access denied. You can only view students assigned to you.');
                }
                break;
                
            case 6: // Telecaller
                // Can only access students they created
                if ($student->created_by != $currentUserId) {
                    abort(403, 'Access denied. You can only view students you created.');
                }
                break;
                
            default:
                abort(403, 'Access denied.');
        }
    }
}
