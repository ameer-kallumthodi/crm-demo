<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Get payments for a specific invoice with payment receipt data
     *
     * @param Request $request
     * @param int $invoiceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvoicePayments(Request $request, $invoiceId)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            $invoice = Invoice::with([
                'course',
                'batch',
                'student.lead',
                'paymentLinks' => function ($query) {
                    $query->latest();
                },
            ])->findOrFail($invoiceId);
            
            // Check permissions (using authenticated user from request)
            $this->checkInvoiceAccess($invoice, $user);
            
            $payments = Payment::with(['createdBy', 'approvedBy', 'rejectedBy'])
                ->where('invoice_id', $invoiceId)
                ->orderBy('created_at', 'desc')
                ->get();

            // Find the first payment (oldest approved payment) for tax invoice
            $firstPayment = Payment::where('invoice_id', $invoiceId)
                ->where('status', 'Approved')
                ->orderBy('created_at', 'asc')
                ->first();

            // Format payments with receipt data
            $formattedPayments = $payments->map(function ($payment) {
                $paymentData = [
                    'id' => $payment->id,
                    'amount_paid' => (float) $payment->amount_paid,
                    'previous_balance' => (float) $payment->previous_balance,
                    'payment_type' => $payment->payment_type,
                    'transaction_id' => $payment->transaction_id,
                    'status' => $payment->status,
                    'approved_date' => $payment->approved_date,
                    'rejected_date' => $payment->rejected_date,
                    'rejection_remarks' => $payment->rejection_remarks,
                    'file_upload' => $payment->file_upload ? asset('storage/' . $payment->file_upload) : null,
                    'created_at' => $payment->created_at,
                    'updated_at' => $payment->updated_at,
                    'created_by' => $payment->createdBy ? [
                        'id' => $payment->createdBy->id,
                        'name' => $payment->createdBy->name,
                    ] : null,
                    'approved_by' => $payment->approvedBy ? [
                        'id' => $payment->approvedBy->id,
                        'name' => $payment->approvedBy->name,
                    ] : null,
                    'rejected_by' => $payment->rejectedBy ? [
                        'id' => $payment->rejectedBy->id,
                        'name' => $payment->rejectedBy->name,
                    ] : null,
                ];

                // Add payment receipt data for approved payments
                if ($payment->status === 'Approved') {
                    $amountInWords = $this->numberToWords($payment->amount_paid);
                    $paymentData['receipt'] = [
                        'amount_in_words' => $amountInWords . ' Rupees only',
                        'can_generate_receipt' => true,
                    ];
                } else {
                    $paymentData['receipt'] = [
                        'amount_in_words' => null,
                        'can_generate_receipt' => false,
                    ];
                }

                return $paymentData;
            });

            // Format payment links
            $formattedPaymentLinks = $invoice->paymentLinks->map(function ($link) {
                return [
                    'id' => $link->id,
                    'amount' => (float) $link->amount,
                    'description' => $link->description,
                    'status' => $link->status,
                    'short_url' => $link->short_url,
                    'razorpay_id' => $link->razorpay_id,
                    'created_at' => $link->created_at,
                ];
            });

            return response()->json([
                'status' => true,
                'data' => [
                    'invoice' => [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'invoice_type' => $invoice->invoice_type,
                        'invoice_date' => $invoice->invoice_date,
                        'total_amount' => (float) $invoice->total_amount,
                        'paid_amount' => (float) $invoice->paid_amount,
                        'pending_amount' => (float) $invoice->pending_amount,
                        'status' => $invoice->status,
                        'course' => $invoice->course ? [
                            'id' => $invoice->course->id,
                            'title' => $invoice->course->title,
                        ] : null,
                        'batch' => $invoice->batch ? [
                            'id' => $invoice->batch->id,
                            'title' => $invoice->batch->title,
                        ] : null,
                        'service_name' => $invoice->service_name,
                        'service_amount' => $invoice->service_amount ? (float) $invoice->service_amount : null,
                    ],
                    'student' => [
                        'id' => $invoice->student->id,
                        'name' => $invoice->student->name,
                        'phone' => $invoice->student->phone,
                        'email' => $invoice->student->email,
                        'register_number' => $invoice->student->register_number,
                    ],
                    'payments' => $formattedPayments,
                    'payment_links' => $formattedPaymentLinks,
                    'first_payment' => $firstPayment ? [
                        'id' => $firstPayment->id,
                        'can_generate_tax_invoice' => $invoice->invoice_type === 'course',
                    ] : null,
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invoice not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user has access to the invoice
     */
    private function checkInvoiceAccess($invoice, $user)
    {
        $currentUserRole = $user->role_id;
        $isTeamLead = $user->is_team_lead == 1;
        
        // Get role title for specific role checks
        $role = \App\Models\UserRole::find($currentUserRole);
        $roleTitle = $role ? $role->title : '';
        
        // Team Lead: Can access all invoices
        if ($isTeamLead) {
            return;
        }
        
        // General Manager: Can access all invoices
        if ($roleTitle === 'General Manager') {
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

    /**
     * Convert number to words (same as PaymentController)
     */
    private function numberToWords($number)
    {
        // Normalize value and support decimal (paise) amounts stored as strings
        $number = (float) $number;
        $number = round($number, 2);

        $rupees = (int) floor($number);
        $paise = (int) round(($number - $rupees) * 100);

        $words = $this->convertNumberPortionToWords($rupees);

        if ($paise > 0) {
            $words .= ' and ' . $this->convertNumberPortionToWords($paise) . ' Paise';
        }

        return trim($words);
    }

    /**
     * Recursively convert integer portion to words.
     */
    private function convertNumberPortionToWords(int $number): string
    {
        $ones = [
            0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen'
        ];

        $tens = [
            20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty',
            60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
        ];

        if ($number === 0) {
            return 'Zero';
        }

        if ($number < 20) {
            return $ones[$number];
        }

        if ($number < 100) {
            $tensDigit = (int) floor($number / 10) * 10;
            $onesDigit = $number % 10;
            
            if ($onesDigit === 0) {
                return $tens[$tensDigit];
            }
            
            return $tens[$tensDigit] . ' ' . $ones[$onesDigit];
        }

        if ($number < 1000) {
            $hundreds = (int) floor($number / 100);
            $remainder = $number % 100;
            
            $result = $ones[$hundreds] . ' Hundred';
            
            if ($remainder > 0) {
                $result .= ' ' . $this->convertNumberPortionToWords($remainder);
            }
            
            return $result;
        }

        if ($number < 100000) {
            $thousands = (int) floor($number / 1000);
            $remainder = $number % 1000;
            
            $result = $this->convertNumberPortionToWords($thousands) . ' Thousand';
            
            if ($remainder > 0) {
                $result .= ' ' . $this->convertNumberPortionToWords($remainder);
            }
            
            return $result;
        }

        if ($number < 10000000) {
            $lakhs = (int) floor($number / 100000);
            $remainder = $number % 100000;
            
            $result = $this->convertNumberPortionToWords($lakhs) . ' Lakh';
            
            if ($remainder > 0) {
                $result .= ' ' . $this->convertNumberPortionToWords($remainder);
            }
            
            return $result;
        }

        // For crores
        $crores = (int) floor($number / 10000000);
        $remainder = $number % 10000000;
        
        $result = $this->convertNumberPortionToWords($crores) . ' Crore';
        
        if ($remainder > 0) {
            $result .= ' ' . $this->convertNumberPortionToWords($remainder);
        }
        
        return $result;
    }
}
