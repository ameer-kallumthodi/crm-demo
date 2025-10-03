<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\ConvertedLead;
use App\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments for a specific invoice
     */
    public function index(Request $request, $invoiceId)
    {
        $invoice = Invoice::with(['course', 'student.lead'])->findOrFail($invoiceId);
        
        // Check permissions
        $this->checkInvoiceAccess($invoice);
        
        $payments = Payment::with(['createdBy'])
            ->where('invoice_id', $invoiceId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Find the first payment (oldest approved payment) for tax invoice
        $firstPayment = Payment::where('invoice_id', $invoiceId)
            ->where('status', 'Approved')
            ->orderBy('created_at', 'asc')
            ->first();

        return view('admin.payments.index', compact('invoice', 'payments', 'firstPayment'));
    }

    /**
     * Show the form for creating a new payment
     */
    public function create($invoiceId)
    {
        $invoice = Invoice::with(['course', 'student.lead'])->findOrFail($invoiceId);
        
        // Check permissions
        $this->checkInvoiceAccess($invoice);

        return view('admin.payments.create', compact('invoice'));
    }

    /**
     * Store a newly created payment
     */
    public function store(Request $request, $invoiceId)
    {
        $validator = Validator::make($request->all(), [
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_type' => 'required|in:Cash,Online,Bank,Cheque,Card,Other',
            'transaction_id' => 'nullable|string|max:255',
            'file_upload' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $invoice = Invoice::findOrFail($invoiceId);
            
            // Check if there's any pending payment
            $pendingPayment = Payment::where('invoice_id', $invoiceId)
                ->where('status', 'Pending Approval')
                ->first();
                
            if ($pendingPayment) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot add new payment. There is already a pending payment waiting for approval.'
                    ], 400);
                }
                
                return redirect()->back()
                    ->with('message_danger', 'Cannot add new payment. There is already a pending payment waiting for approval.')
                    ->withInput();
            }
            
            // Check if payment amount doesn't exceed remaining balance
            $remainingBalance = $invoice->total_amount - $invoice->paid_amount;
            if ($request->amount_paid > $remainingBalance) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment amount cannot exceed the remaining balance of ' . number_format($remainingBalance, 2)
                    ], 400);
                }
                
                return redirect()->back()
                    ->with('message_danger', 'Payment amount cannot exceed the remaining balance of ' . number_format($remainingBalance, 2))
                    ->withInput();
            }

            // Calculate previous balance (sum of all approved payments)
            $previousBalance = Payment::where('invoice_id', $invoiceId)
                ->where('status', 'Approved')
                ->sum('amount_paid');

            $filePath = null;
            if ($request->hasFile('file_upload')) {
                $file = $request->file('file_upload');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('payments', $fileName, 'public');
            }

            $payment = Payment::create([
                'invoice_id' => $invoiceId,
                'amount_paid' => $request->amount_paid,
                'previous_balance' => $previousBalance,
                'payment_type' => $request->payment_type,
                'transaction_id' => $request->transaction_id,
                'file_upload' => $filePath,
                'status' => 'Pending Approval',
                'created_by' => AuthHelper::getCurrentUserId(),
            ]);

            // Don't update invoice until payment is approved

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment added successfully!'
                ]);
            }
            
            return redirect()->route('admin.payments.index', $invoiceId)
                ->with('message_success', 'Payment added successfully!');

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while adding the payment. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->with('message_danger', 'An error occurred while adding the payment. Please try again.')
                ->withInput();
        }
    }

    /**
     * Approve a payment
     */
    public function approve($id)
    {
        try {
            $payment = Payment::findOrFail($id);
            
            // Check permissions
            $this->checkInvoiceAccess($payment->invoice);
            
            $payment->approve();

            return redirect()->back()
                ->with('message_success', 'Payment approved successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message_danger', 'An error occurred while approving the payment. Please try again.');
        }
    }

    /**
     * Reject a payment
     */
    public function reject($id)
    {
        try {
            $payment = Payment::findOrFail($id);
            
            // Check permissions
            $this->checkInvoiceAccess($payment->invoice);
            
            $payment->reject();

            return redirect()->back()
                ->with('message_success', 'Payment rejected successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message_danger', 'An error occurred while rejecting the payment. Please try again.');
        }
    }

    /**
     * Display the specified payment
     */
    public function show($id)
    {
        $payment = Payment::with(['invoice.course', 'invoice.student.lead', 'createdBy'])
            ->findOrFail($id);
        
        // Check permissions
        $this->checkInvoiceAccess($payment->invoice);

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * View payment file
     */
    public function viewFile($id)
    {
        $payment = Payment::findOrFail($id);
        
        // Check permissions
        $this->checkInvoiceAccess($payment->invoice);
        
        if (!$payment->file_upload || !Storage::disk('public')->exists($payment->file_upload)) {
            return redirect()->back()
                ->with('message_danger', 'File not found.');
        }

        $filePath = storage_path('app/public/' . $payment->file_upload);
        $mimeType = mime_content_type($filePath);
        
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($payment->file_upload) . '"'
        ]);
    }

    /**
     * Download payment file
     */
    public function downloadFile($id)
    {
        $payment = Payment::findOrFail($id);
        
        // Check permissions
        $this->checkInvoiceAccess($payment->invoice);
        
        if (!$payment->file_upload || !Storage::disk('public')->exists($payment->file_upload)) {
            return redirect()->back()
                ->with('message_danger', 'File not found.');
        }

        return response()->download(storage_path('app/public/' . $payment->file_upload), basename($payment->file_upload));
    }

    /**
     * Show tax invoice for payment
     */
    public function taxInvoice($id)
    {
        $payment = Payment::with(['invoice.student', 'invoice.course'])
            ->findOrFail($id);
        
        // Check permissions
        $this->checkInvoiceAccess($payment->invoice);
        
        // Check if payment is approved
        if ($payment->status !== 'Approved') {
            return redirect()->back()
                ->with('message_danger', 'Tax invoice can only be viewed for approved payments.');
        }
        
        // Add number to words conversion
        $payment->amount_in_words = $this->numberToWords($payment->amount_paid);
        $payment->total_amount_in_words = $this->numberToWords($payment->invoice->total_amount);
        
        return view('admin.payments.tax-invoice', compact('payment'));
    }

    /**
     * Generate PDF for tax invoice
     */
    public function taxInvoicePdf($id)
    {
        $payment = Payment::with(['invoice.student', 'invoice.course'])
            ->findOrFail($id);
        
        // Check permissions
        $this->checkInvoiceAccess($payment->invoice);
        
        // Check if payment is approved
        if ($payment->status !== 'Approved') {
            return redirect()->back()
                ->with('message_danger', 'Tax invoice PDF can only be generated for approved payments.');
        }
        
        // Add number to words conversion
        $payment->amount_in_words = $this->numberToWords($payment->amount_paid);
        $payment->total_amount_in_words = $this->numberToWords($payment->invoice->total_amount);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.payments.tax-invoice-pdf-inline', compact('payment'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial',
            'isPhpEnabled' => true,
            'isJavascriptEnabled' => false,
            'dpi' => 96,
            'defaultMediaType' => 'print',
            'isFontSubsettingEnabled' => true,
            'debugKeepTemp' => false,
            'debugCss' => false,
            'debugLayout' => false,
            'debugLayoutLines' => false,
            'debugLayoutBlocks' => false,
            'debugLayoutInline' => false,
            'debugLayoutPaddingBox' => false,
        ]);
        
        $filename = 'tax_invoice_' . $payment->invoice->invoice_number . '_' . $payment->id . '.pdf';
        
        return $pdf->stream($filename);
    }

    /**
     * Show payment receipt for payment
     */
    public function paymentReceipt($id)
    {
        $payment = Payment::with(['invoice.student', 'invoice.course'])
            ->findOrFail($id);
        
        // Check permissions
        $this->checkInvoiceAccess($payment->invoice);
        
        // Check if payment is approved
        if ($payment->status !== 'Approved') {
            return redirect()->back()
                ->with('message_danger', 'Payment receipt can only be viewed for approved payments.');
        }
        
        // Add number to words conversion
        $payment->amount_in_words = $this->numberToWords($payment->amount_paid);
        
        return view('admin.payments.payment-receipt', compact('payment'));
    }

    /**
     * Generate PDF for payment receipt
     */
    public function paymentReceiptPdf($id)
    {
        $payment = Payment::with(['invoice.student', 'invoice.course'])
            ->findOrFail($id);
        
        // Check permissions
        $this->checkInvoiceAccess($payment->invoice);
        
        // Check if payment is approved
        if ($payment->status !== 'Approved') {
            return redirect()->back()
                ->with('message_danger', 'Payment receipt PDF can only be generated for approved payments.');
        }
        
        // Add number to words conversion
        $payment->amount_in_words = $this->numberToWords($payment->amount_paid);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.payments.payment-receipt-pdf-inline', compact('payment'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial',
            'isPhpEnabled' => true,
            'isJavascriptEnabled' => false,
            'dpi' => 96,
            'defaultMediaType' => 'print',
            'isFontSubsettingEnabled' => true,
            'debugKeepTemp' => false,
            'debugCss' => false,
            'debugLayout' => false,
            'debugLayoutLines' => false,
            'debugLayoutBlocks' => false,
            'debugLayoutInline' => false,
            'debugLayoutPaddingBox' => false,
        ]);
        
        $filename = 'payment_receipt_' . $payment->invoice->invoice_number . '_' . $payment->id . '.pdf';
        
        return $pdf->stream($filename);
    }

    /**
     * Convert number to words
     */
    private function numberToWords($number)
    {
        $ones = array(
            0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen'
        );
        
        $tens = array(
            20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty',
            60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
        );
        
        $hundreds = array(
            100 => 'Hundred', 1000 => 'Thousand', 100000 => 'Lakh', 10000000 => 'Crore'
        );
        
        if ($number < 20) {
            return $ones[$number];
        } elseif ($number < 100) {
            return $tens[10 * floor($number / 10)] . ($number % 10 ? ' ' . $ones[$number % 10] : '');
        } elseif ($number < 1000) {
            return $ones[floor($number / 100)] . ' Hundred' . ($number % 100 ? ' ' . $this->numberToWords($number % 100) : '');
        } elseif ($number < 100000) {
            return $this->numberToWords(floor($number / 1000)) . ' Thousand' . ($number % 1000 ? ' ' . $this->numberToWords($number % 1000) : '');
        } elseif ($number < 10000000) {
            return $this->numberToWords(floor($number / 100000)) . ' Lakh' . ($number % 100000 ? ' ' . $this->numberToWords($number % 100000) : '');
        } else {
            return $this->numberToWords(floor($number / 10000000)) . ' Crore' . ($number % 10000000 ? ' ' . $this->numberToWords($number % 10000000) : '');
        }
    }

    /**
     * Auto-create payment during lead conversion
     */
    public function autoCreate($invoiceId, $amount, $paymentType, $transactionId = null, $fileUpload = null)
    {
        try {
            $invoice = Invoice::findOrFail($invoiceId);
            
            // Calculate previous balance (sum of all approved payments)
            $previousBalance = Payment::where('invoice_id', $invoiceId)
                ->where('status', 'Approved')
                ->sum('amount_paid');
            
            $filePath = null;
            if ($fileUpload) {
                $fileName = time() . '_' . $fileUpload->getClientOriginalName();
                $filePath = $fileUpload->storeAs('payments', $fileName, 'public');
            }

            $payment = Payment::create([
                'invoice_id' => $invoiceId,
                'amount_paid' => $amount,
                'previous_balance' => $previousBalance,
                'payment_type' => $paymentType,
                'transaction_id' => $transactionId,
                'file_upload' => $filePath,
                'status' => 'Pending Approval', // Keep as pending for manual approval
                'created_by' => AuthHelper::getCurrentUserId(),
            ]);

            // Don't update invoice until payment is approved

            return $payment;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check if user has access to the invoice
     */
    private function checkInvoiceAccess($invoice)
    {
        $currentUserId = AuthHelper::getCurrentUserId();
        $currentUserRole = AuthHelper::getCurrentUserRole();
        
        // Check if user is team lead using the helper method
        if (\App\Helpers\RoleHelper::is_team_lead()) {
            // Team Lead can access all invoices
            return;
        }
        
        switch ($currentUserRole) {
            case 1: // Super Admin
            case 2: // Admin
            case 3: // Telecaller
            case 4: // Admission Counsellor
            case 5: // Academic Assistant
            case 6: // Finance
            case 7: // Post-sales
                // Can access all invoices
                break;
                
            default:
                abort(403, 'Access denied.');
        }
    }
}
