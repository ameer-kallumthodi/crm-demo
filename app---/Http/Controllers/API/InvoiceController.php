<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\ConvertedLead;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Get invoices for a specific student
     *
     * @param Request $request
     * @param int $studentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStudentInvoices(Request $request, $studentId)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            $student = ConvertedLead::with(['course', 'lead'])->findOrFail($studentId);
            
            // Check permissions (using authenticated user from request)
            $this->checkStudentAccess($student, $user);
            
            $invoices = Invoice::with(['course', 'batch', 'payments' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
                ->where('student_id', $studentId)
                ->orderBy('created_at', 'desc')
                ->get();

            // Calculate summary
            $summary = [
                'total_invoices' => $invoices->count(),
                'total_amount' => (float) $invoices->sum('total_amount'),
                'total_paid' => (float) $invoices->sum('paid_amount'),
                'total_pending' => (float) ($invoices->sum('total_amount') - $invoices->sum('paid_amount')),
            ];

            // Format invoices for API response
            $formattedInvoices = $invoices->map(function ($invoice) {
                return [
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
                    'created_at' => $invoice->created_at,
                    'updated_at' => $invoice->updated_at,
                ];
            });

            return response()->json([
                'status' => true,
                'data' => [
                    'student' => [
                        'id' => $student->id,
                        'name' => $student->name,
                        'phone' => $student->phone,
                        'email' => $student->email,
                        'register_number' => $student->register_number,
                        'course' => $student->course ? [
                            'id' => $student->course->id,
                            'title' => $student->course->title,
                        ] : null,
                    ],
                    'invoices' => $formattedInvoices,
                    'summary' => $summary,
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Student not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user has access to the student
     */
    private function checkStudentAccess($student, $user)
    {
        $currentUserId = $user->id;
        $currentUserRole = $user->role_id;
        $isTeamLead = $user->is_team_lead == 1;
        
        // Get role title for specific role checks
        $role = \App\Models\UserRole::find($currentUserRole);
        $roleTitle = $role ? $role->title : '';
        
        // Senior Manager: Can access all students
        if ($currentUserRole == 3 && $user->is_senior_manager == 1) {
            return;
        }
        
        // General Manager: Can access all students
        if ($roleTitle === 'General Manager') {
            return;
        }
        
        // Check team lead
        if ($isTeamLead) {
            // Team Lead: Can access students from their team
            $teamId = $user->team_id;
            if ($teamId) {
                $teamMemberIds = \App\Models\User::where('team_id', $teamId)
                    ->pluck('id')
                    ->toArray();
                    
                if (!in_array($student->created_by, $teamMemberIds)) {
                    abort(403, 'Access denied. You can only view students from your team.');
                }
            } else {
                // If no team assigned, only show their own students
                if ($student->created_by != $currentUserId) {
                    abort(403, 'Access denied. You can only view students you created.');
                }
            }
            return;
        }
        
        switch ($currentUserRole) {
            case 1: // Super Admin
            case 2: // Admin
            case 4: // Admission Counsellor
            case 6: // Finance
            case 7: // Post Sales
                // Can access all students
                break;
                
            case 5: // Academic Assistant
                // Can only access students assigned to them
                if ($student->academic_assistant_id != $currentUserId) {
                    abort(403, 'Access denied. You can only view students assigned to you.');
                }
                break;
                
            case 3: // Telecaller
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
