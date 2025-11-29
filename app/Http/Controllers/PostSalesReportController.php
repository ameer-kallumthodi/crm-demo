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

        // Date range filter (from_date & to_date)
        $fromDateInput = $request->get('from_date');
        $toDateInput = $request->get('to_date');

        // Default range: 1st day of current month to today
        if (!$fromDateInput || !$toDateInput) {
            $fromDate = Carbon::now()->startOfMonth();
            $toDate = Carbon::now();
        } else {
            $fromDate = Carbon::createFromFormat('Y-m-d', $fromDateInput)->startOfDay();
            $toDate = Carbon::createFromFormat('Y-m-d', $toDateInput)->endOfDay();
        }

        // Normalized strings for form values
        $fromDateStr = $fromDate->format('Y-m-d');
        $toDateStr = $toDate->format('Y-m-d');

        // Get all post sale users (role_id = 7)
        $postSaleUsers = User::where('role_id', 7)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $reports = [];

        foreach ($postSaleUsers as $postSaleUser) {
            // Get payments collected by this post sale user in the selected date range
            $payments = Payment::where('collected_by', $postSaleUser->id)
                ->where('status', 'Approved')
                ->whereBetween('created_at', [$fromDate->copy()->startOfDay(), $toDate->copy()->endOfDay()])
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

        return view('admin.reports.post-sales-month-ways', [
            'reports' => $reports,
            'fromDate' => $fromDateStr,
            'toDate' => $toDateStr,
        ]);
    }

    /**
     * Total Monthly Report
     * Shows course-wise total for the month (only post-sales users data)
     */
    public function totalMonthlyReport(Request $request)
    {
        $this->checkAccess();

        // Date range filter (from_date & to_date)
        $fromDateInput = $request->get('from_date');
        $toDateInput = $request->get('to_date');

        // Default range: 1st day of current month to today
        if (!$fromDateInput || !$toDateInput) {
            $fromDate = Carbon::now()->startOfMonth();
            $toDate = Carbon::now();
        } else {
            $fromDate = Carbon::createFromFormat('Y-m-d', $fromDateInput)->startOfDay();
            $toDate = Carbon::createFromFormat('Y-m-d', $toDateInput)->endOfDay();
        }

        // Normalized strings for form values
        $fromDateStr = $fromDate->format('Y-m-d');
        $toDateStr = $toDate->format('Y-m-d');

        // Get all post-sales users (role_id = 7)
        $postSaleUserIds = User::where('role_id', 7)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        // Get all approved payments in the month collected by post-sales users only
        $payments = Payment::where('status', 'Approved')
            ->whereIn('collected_by', $postSaleUserIds)
            ->whereBetween('created_at', [$fromDate->copy()->startOfDay(), $toDate->copy()->endOfDay()])
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

        return view('admin.reports.total-monthly', [
            'reportData' => $reportData,
            'grandTotal' => $grandTotal,
            'grandTotalStudents' => $grandTotalStudents,
            'fromDate' => $fromDateStr,
            'toDate' => $toDateStr,
        ]);
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

        // Optional date range filter
        $fromDateInput = $request->get('from_date');
        $toDateInput = $request->get('to_date');

        $fromDate = null;
        $toDate = null;

        if ($fromDateInput) {
            $fromDate = Carbon::createFromFormat('Y-m-d', $fromDateInput)->startOfDay();
        }
        if ($toDateInput) {
            $toDate = Carbon::createFromFormat('Y-m-d', $toDateInput)->endOfDay();
        }

        $reportData = [];
        $grandTotal = 0;
        $grandTotalStudents = 0;

        if ($selectedPostSalesId) {
            $payments = Payment::with(['invoice.course', 'invoice.student'])
                ->where('status', 'Approved')
                ->where('collected_by', $selectedPostSalesId);

            // Apply optional date filters
            if ($fromDate) {
                $payments->where('created_at', '>=', $fromDate);
            }
            if ($toDate) {
                $payments->where('created_at', '<=', $toDate);
            }

            $payments = $payments->get();

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

        return view('admin.reports.bde-collected-amount-course-ways', [
            'reportData' => $reportData,
            'grandTotal' => $grandTotal,
            'grandTotalStudents' => $grandTotalStudents,
            'postSalesUsers' => $postSalesUsers,
            'selectedPostSalesId' => $selectedPostSalesId,
            'fromDate' => $fromDateInput,
            'toDate' => $toDateInput,
        ]);
    }
}

