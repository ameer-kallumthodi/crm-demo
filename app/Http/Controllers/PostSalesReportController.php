<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\ConvertedLead;
use App\Models\Course;
use App\Helpers\RoleHelper;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

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
     * Check if user has access to finance reports (telecallers sales report)
     */
    private function checkFinanceAccess()
    {
        if (!RoleHelper::is_finance() && 
            !RoleHelper::is_admin_or_super_admin()) {
            abort(403, 'Access denied. You do not have permission to view finance reports.');
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

    /**
     * Telecallers Sales Report
     * Shows report for each telecaller with sales count, total sale amount, and received at sale (DP)
     */
    public function telecallersSalesReport(Request $request)
    {
        $this->checkFinanceAccess();

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

        // Telecaller filter
        $selectedTelecallerId = $request->get('telecaller_id');

        // Get all telecallers (role_id = 3)
        $telecallersQuery = User::where('role_id', 3)
            ->where('is_active', true)
            ->orderBy('name');

        // Filter by selected telecaller if provided
        if ($selectedTelecallerId) {
            $telecallersQuery->where('id', $selectedTelecallerId);
        }

        $telecallers = $telecallersQuery->get();

        $reports = [];

        foreach ($telecallers as $telecaller) {
            // Get converted leads for this telecaller within date range
            // Converted leads are linked to leads via lead_id, and leads have telecaller_id
            $convertedLeads = \App\Models\ConvertedLead::whereHas('lead', function($query) use ($telecaller) {
                $query->where('telecaller_id', $telecaller->id)
                      ->where('is_converted', true);
            })
            ->whereBetween('created_at', [$fromDate->copy()->startOfDay(), $toDate->copy()->endOfDay()])
            ->get();

            // Sales count (converted leads count)
            $salesCount = $convertedLeads->count();

            // Total Sale Amount (sum of invoice total_amount for converted leads)
            // Get all invoices for converted leads (invoices are created at conversion time)
            $convertedLeadIds = $convertedLeads->pluck('id')->toArray();
            $totalSaleAmount = 0;
            if (!empty($convertedLeadIds)) {
                $totalSaleAmount = Invoice::whereIn('student_id', $convertedLeadIds)
                    ->sum('total_amount');
            }

            // Received at Sale (DP) - payments collected_by telecaller and approved
            $receivedAtSale = Payment::where('collected_by', $telecaller->id)
                ->where('status', 'Approved')
                ->whereBetween('created_at', [$fromDate->copy()->startOfDay(), $toDate->copy()->endOfDay()])
                ->sum('amount_paid');

            $reports[] = [
                'telecaller' => $telecaller,
                'sales_count' => $salesCount,
                'total_sale_amount' => $totalSaleAmount,
                'received_at_sale' => $receivedAtSale,
            ];
        }

        return view('admin.reports.telecallers-sales', [
            'reports' => $reports,
            'telecallers' => User::where('role_id', 3)->where('is_active', true)->orderBy('name')->get(),
            'selectedTelecallerId' => $selectedTelecallerId,
            'fromDate' => $fromDateStr,
            'toDate' => $toDateStr,
        ]);
    }

    /**
     * Course Wise Sales Report
     * Shows report for each course with sales count, total sale amount, and received amount
     */
    public function courseWiseSalesReport(Request $request)
    {
        $this->checkFinanceAccess();

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

        // Course filter
        $selectedCourseId = $request->get('course_id');

        // Get all courses
        $coursesQuery = Course::orderBy('title');
        
        // Filter by selected course if provided
        if ($selectedCourseId) {
            $coursesQuery->where('id', $selectedCourseId);
        }

        $courses = $coursesQuery->get();

        $reports = [];

        foreach ($courses as $course) {
            // Get converted leads for this course within date range
            $convertedLeads = ConvertedLead::where('course_id', $course->id)
                ->whereBetween('created_at', [$fromDate->copy()->startOfDay(), $toDate->copy()->endOfDay()])
                ->get();

            // Sales count (converted leads count)
            $salesCount = $convertedLeads->count();

            // Total Sale Amount (sum of invoice total_amount for converted leads)
            $convertedLeadIds = $convertedLeads->pluck('id')->toArray();
            $totalSaleAmount = 0;
            if (!empty($convertedLeadIds)) {
                $totalSaleAmount = Invoice::whereIn('student_id', $convertedLeadIds)
                    ->sum('total_amount');
            }

            // Received Amount - sum of approved payments for invoices of this course
            $receivedAmount = 0;
            if (!empty($convertedLeadIds)) {
                $invoiceIds = Invoice::whereIn('student_id', $convertedLeadIds)
                    ->pluck('id')
                    ->toArray();
                
                if (!empty($invoiceIds)) {
                    $receivedAmount = Payment::whereIn('invoice_id', $invoiceIds)
                        ->where('status', 'Approved')
                        ->whereBetween('created_at', [$fromDate->copy()->startOfDay(), $toDate->copy()->endOfDay()])
                        ->sum('amount_paid');
                }
            }

            $reports[] = [
                'course' => $course,
                'sales_count' => $salesCount,
                'total_sale_amount' => $totalSaleAmount,
                'received_amount' => $receivedAmount,
            ];
        }

        return view('admin.reports.course-wise-sales', [
            'reports' => $reports,
            'courses' => Course::orderBy('title')->get(),
            'selectedCourseId' => $selectedCourseId,
            'fromDate' => $fromDateStr,
            'toDate' => $toDateStr,
        ]);
    }

    /**
     * Export Telecallers Sales Report to PDF
     */
    public function exportTelecallersSalesPdf(Request $request)
    {
        $this->checkFinanceAccess();

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

        // Telecaller filter
        $selectedTelecallerId = $request->get('telecaller_id');

        // Get all telecallers (role_id = 3)
        $telecallersQuery = User::where('role_id', 3)
            ->where('is_active', true)
            ->orderBy('name');

        // Filter by selected telecaller if provided
        if ($selectedTelecallerId) {
            $telecallersQuery->where('id', $selectedTelecallerId);
        }

        $telecallers = $telecallersQuery->get();

        $reports = [];

        foreach ($telecallers as $telecaller) {
            // Get converted leads for this telecaller within date range
            $convertedLeads = ConvertedLead::whereHas('lead', function($query) use ($telecaller) {
                $query->where('telecaller_id', $telecaller->id)
                      ->where('is_converted', true);
            })
            ->whereBetween('created_at', [$fromDate->copy()->startOfDay(), $toDate->copy()->endOfDay()])
            ->get();

            // Sales count (converted leads count)
            $salesCount = $convertedLeads->count();

            // Total Sale Amount (sum of invoice total_amount for converted leads)
            $convertedLeadIds = $convertedLeads->pluck('id')->toArray();
            $totalSaleAmount = 0;
            if (!empty($convertedLeadIds)) {
                $totalSaleAmount = Invoice::whereIn('student_id', $convertedLeadIds)
                    ->sum('total_amount');
            }

            // Received at Sale (DP) - payments collected_by telecaller and approved
            $receivedAtSale = Payment::where('collected_by', $telecaller->id)
                ->where('status', 'Approved')
                ->whereBetween('created_at', [$fromDate->copy()->startOfDay(), $toDate->copy()->endOfDay()])
                ->sum('amount_paid');

            $reports[] = [
                'telecaller' => $telecaller,
                'sales_count' => $salesCount,
                'total_sale_amount' => $totalSaleAmount,
                'received_at_sale' => $receivedAtSale,
            ];
        }

        $pdf = Pdf::loadView('admin.reports.exports.telecallers-sales-pdf', [
            'reports' => $reports,
            'fromDate' => $fromDateStr,
            'toDate' => $toDateStr,
            'generatedAt' => now()->format('Y-m-d H:i:s')
        ]);

        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('telecallers-sales-report-' . $fromDateStr . '-to-' . $toDateStr . '.pdf');
    }

    /**
     * Export Course Wise Sales Report to PDF
     */
    public function exportCourseWiseSalesPdf(Request $request)
    {
        $this->checkFinanceAccess();

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

        // Course filter
        $selectedCourseId = $request->get('course_id');

        // Get all courses
        $coursesQuery = Course::orderBy('title');
        
        // Filter by selected course if provided
        if ($selectedCourseId) {
            $coursesQuery->where('id', $selectedCourseId);
        }

        $courses = $coursesQuery->get();

        $reports = [];

        foreach ($courses as $course) {
            // Get converted leads for this course within date range
            $convertedLeads = ConvertedLead::where('course_id', $course->id)
                ->whereBetween('created_at', [$fromDate->copy()->startOfDay(), $toDate->copy()->endOfDay()])
                ->get();

            // Sales count (converted leads count)
            $salesCount = $convertedLeads->count();

            // Total Sale Amount (sum of invoice total_amount for converted leads)
            $convertedLeadIds = $convertedLeads->pluck('id')->toArray();
            $totalSaleAmount = 0;
            if (!empty($convertedLeadIds)) {
                $totalSaleAmount = Invoice::whereIn('student_id', $convertedLeadIds)
                    ->sum('total_amount');
            }

            // Received Amount - sum of approved payments for invoices of this course
            $receivedAmount = 0;
            if (!empty($convertedLeadIds)) {
                $invoiceIds = Invoice::whereIn('student_id', $convertedLeadIds)
                    ->pluck('id')
                    ->toArray();
                
                if (!empty($invoiceIds)) {
                    $receivedAmount = Payment::whereIn('invoice_id', $invoiceIds)
                        ->where('status', 'Approved')
                        ->whereBetween('created_at', [$fromDate->copy()->startOfDay(), $toDate->copy()->endOfDay()])
                        ->sum('amount_paid');
                }
            }

            $reports[] = [
                'course' => $course,
                'sales_count' => $salesCount,
                'total_sale_amount' => $totalSaleAmount,
                'received_amount' => $receivedAmount,
            ];
        }

        $pdf = Pdf::loadView('admin.reports.exports.course-wise-sales-pdf', [
            'reports' => $reports,
            'fromDate' => $fromDateStr,
            'toDate' => $toDateStr,
            'generatedAt' => now()->format('Y-m-d H:i:s')
        ]);

        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('course-wise-sales-report-' . $fromDateStr . '-to-' . $toDateStr . '.pdf');
    }
}

