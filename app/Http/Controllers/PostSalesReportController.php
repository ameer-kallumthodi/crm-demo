<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
use App\Models\Invoice;
use App\Helpers\RoleHelper;
use Carbon\Carbon;

class PostSalesReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    /**
     * Check if user has access to post sales reports
     */
    private function checkAccess()
    {
        if (!RoleHelper::is_finance() && 
            !RoleHelper::is_admin_or_super_admin() && 
            !RoleHelper::is_post_sales_head()) {
            abort(403, 'Access denied. You do not have permission to view post sales reports.');
        }
    }

    /**
     * Post Sales Month Ways Report
     * Shows report for each post sale user with course-wise breakdown
     */
    public function postSalesMonthWaysReport(Request $request)
    {
        $this->checkAccess();
        
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        
        // Parse month to get start and end dates
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        // Get all post sale users (role_id = 7)
        $postSaleUsers = User::where('role_id', 7)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $reports = [];

        foreach ($postSaleUsers as $postSaleUser) {
            // Get payments collected by this post sale user in the month
            $payments = Payment::where('collected_by', $postSaleUser->id)
                ->where('status', 'Approved')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->with(['invoice.course', 'invoice.student'])
                ->get();

            // Group by course
            $courseData = [];
            foreach ($payments as $payment) {
                if (!$payment->invoice || !$payment->invoice->course) {
                    continue;
                }

                $courseId = $payment->invoice->course_id;
                $courseName = $payment->invoice->course->title;
                $studentId = $payment->invoice->student_id;

                if (!isset($courseData[$courseId])) {
                    $courseData[$courseId] = [
                        'course_name' => $courseName,
                        'student_ids' => [],
                        'total_amount' => 0
                    ];
                }

                // Add unique student ID
                if (!in_array($studentId, $courseData[$courseId]['student_ids'])) {
                    $courseData[$courseId]['student_ids'][] = $studentId;
                }

                // Add amount
                $courseData[$courseId]['total_amount'] += $payment->amount_paid;
            }

            // Convert to array format
            $userReport = [];
            foreach ($courseData as $courseId => $data) {
                $userReport[] = [
                    'course_name' => $data['course_name'],
                    'student_count' => count($data['student_ids']),
                    'total_amount' => $data['total_amount']
                ];
            }

            // Always add user to reports, even if no data
            $reports[] = [
                'user' => $postSaleUser,
                'data' => $userReport
            ];
        }

        return view('admin.reports.post-sales-month-ways', compact('reports', 'month'));
    }

    /**
     * Total Monthly Report
     * Shows course-wise total for the month
     */
    public function totalMonthlyReport(Request $request)
    {
        $this->checkAccess();
        
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        
        // Parse month to get start and end dates
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        // Get all approved payments in the month
        $payments = Payment::where('status', 'Approved')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['invoice.course', 'invoice.student'])
            ->get();

        // Group by course
        $courseData = [];
        foreach ($payments as $payment) {
            if (!$payment->invoice || !$payment->invoice->course) {
                continue;
            }

            $courseId = $payment->invoice->course_id;
            $courseName = $payment->invoice->course->title;
            $studentId = $payment->invoice->student_id;

            if (!isset($courseData[$courseId])) {
                $courseData[$courseId] = [
                    'course_name' => $courseName,
                    'student_ids' => [],
                    'total_amount' => 0
                ];
            }

            // Add unique student ID
            if (!in_array($studentId, $courseData[$courseId]['student_ids'])) {
                $courseData[$courseId]['student_ids'][] = $studentId;
            }

            // Add amount
            $courseData[$courseId]['total_amount'] += $payment->amount_paid;
        }

        // Convert to array format
        $reportData = [];
        $grandTotal = 0;
        $grandTotalStudents = 0;

        foreach ($courseData as $courseId => $data) {
            $studentCount = count($data['student_ids']);
            $reportData[] = [
                'course_name' => $data['course_name'],
                'student_count' => $studentCount,
                'total_amount' => $data['total_amount']
            ];
            $grandTotal += $data['total_amount'];
            $grandTotalStudents += $studentCount;
        }

        return view('admin.reports.total-monthly', compact('reportData', 'grandTotal', 'grandTotalStudents', 'month'));
    }

    /**
     * BDE Collected Amount Fully Course Ways Report
     * Shows fully paid students count and amount by course
     */
    public function bdeCollectedAmountCourseWaysReport(Request $request)
    {
        $this->checkAccess();
        
        $postSalesUsers = User::where('role_id', 7)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $selectedPostSalesId = $request->get('post_sales_user_id');

        $reportData = [];
        $grandTotal = 0;
        $grandTotalStudents = 0;

        if ($selectedPostSalesId) {
            $payments = Payment::with(['invoice.course', 'invoice.student'])
                ->where('status', 'Approved')
                ->where('collected_by', $selectedPostSalesId)
                ->get();

            $courseData = [];
            foreach ($payments as $payment) {
                if (!$payment->invoice || !$payment->invoice->course) {
                    continue;
                }

                $courseId = $payment->invoice->course_id;
                $courseName = $payment->invoice->course->title;
                $studentId = $payment->invoice->student_id;

                if (!isset($courseData[$courseId])) {
                    $courseData[$courseId] = [
                        'course_name' => $courseName,
                        'student_ids' => [],
                        'total_amount' => 0
                    ];
                }

                if (!in_array($studentId, $courseData[$courseId]['student_ids'])) {
                    $courseData[$courseId]['student_ids'][] = $studentId;
                }

                $courseData[$courseId]['total_amount'] += $payment->amount_paid;
            }

            foreach ($courseData as $courseId => $data) {
                $studentCount = count($data['student_ids']);
                $reportData[] = [
                    'course_name' => $data['course_name'],
                    'student_count' => $studentCount,
                    'total_amount' => $data['total_amount']
                ];
                $grandTotal += $data['total_amount'];
                $grandTotalStudents += $studentCount;
            }
        }

        return view('admin.reports.bde-collected-amount-course-ways', compact(
            'reportData',
            'grandTotal',
            'grandTotalStudents',
            'postSalesUsers',
            'selectedPostSalesId'
        ));
    }
}

