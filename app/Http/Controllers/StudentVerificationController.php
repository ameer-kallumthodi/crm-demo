<?php

namespace App\Http\Controllers;

use App\Models\StudentDetail;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentVerificationController extends Controller
{
    public function toggleVerifyStudent(Request $request, $studentId)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $currentUser = Auth::user();
        $student = StudentDetail::findOrFail($studentId);

        // Toggle verification
        if ($student->is_verified) {
            $student->is_verified = false;
            $student->verified_by = null;
            $student->verified_at = null;
            $student->save();
            
            return response()->json([
                'success' => true,
                'message' => "{$student->name} unverified successfully.",
                'is_verified' => false
            ]);
        } else {
            $student->is_verified = true;
            $student->verified_by = $currentUser->id;
            $student->verified_at = now();
            $student->save();

            // Send verification email
            try {
                MailService::sendNiosStudentVerificationEmail($student, $currentUser);
                
                return response()->json([
                    'success' => true,
                    'message' => "{$student->name} verified. Emails sent to {$currentUser->email} and CAO.",
                    'is_verified' => true
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => "Student verified but email sending failed: " . $e->getMessage(),
                    'is_verified' => true
                ]);
            }
        }
    }

    public function getVerificationStatus($studentId)
    {
        $student = StudentDetail::findOrFail($studentId);
        
        return response()->json([
            'is_verified' => $student->is_verified,
            'verified_at' => $student->verified_at,
            'verified_by' => $student->verified_by
        ]);
    }

    public function bulkVerify(Request $request)
    {
        $studentIds = $request->input('student_ids', []);
        $currentUser = Auth::user();
        $results = [];

        foreach ($studentIds as $studentId) {
            try {
                $student = StudentDetail::findOrFail($studentId);
                
                if (!$student->is_verified) {
                    $student->is_verified = true;
                    $student->verified_by = $currentUser->id;
                    $student->verified_at = now();
                    $student->save();

                    // Send verification email
                    MailService::sendNiosStudentVerificationEmail($student, $currentUser);
                    
                    $results[] = [
                        'id' => $studentId,
                        'name' => $student->name,
                        'status' => 'verified',
                        'message' => 'Successfully verified and email sent'
                    ];
                } else {
                    $results[] = [
                        'id' => $studentId,
                        'name' => $student->name,
                        'status' => 'already_verified',
                        'message' => 'Already verified'
                    ];
                }
            } catch (\Exception $e) {
                $results[] = [
                    'id' => $studentId,
                    'name' => 'Unknown',
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Bulk verification completed',
            'results' => $results
        ]);
    }
}
