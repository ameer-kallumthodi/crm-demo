<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\ConvertedLead;
use App\Models\Course;
use App\Models\LeadStatus;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;

class CourseReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    /**
     * Course-wise Summary Report
     * Shows converted count, leads count, and follow-up count by course
     */
    public function index(Request $request)
    {
        // Check if user has access to reports
        if (!RoleHelper::is_admin() && !RoleHelper::is_super_admin() && !RoleHelper::is_team_lead() && !RoleHelper::is_admission_counsellor()) {
            abort(403, 'Access denied. Admin, Team Lead, or Admission Counsellor access required.');
        }

        // Default date range (last 30 days)
        $fromDate = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $courseId = $request->get('course_id', '');

        // Get all courses for filter
        $courses = Course::select('id', 'title')->orderBy('title')->get();

        // Get course-wise summary data
        $courseSummary = $this->getCourseSummaryData($fromDate, $toDate, $courseId);

        // Get current user role information
        $currentUser = AuthHelper::getCurrentUser();
        $isTeamLead = $currentUser && RoleHelper::is_team_lead();
        $isTelecaller = $currentUser && AuthHelper::isTelecaller();

        return view('admin.reports.course-summary', compact(
            'courseSummary', 'courses', 'fromDate', 'toDate', 'courseId', 'isTeamLead', 'isTelecaller'
        ));
    }

    /**
     * Detailed Course Leads Report
     * Shows all leads for a specific course
     */
    public function courseLeads(Request $request, $courseId)
    {
        // Check if user has access to reports
        if (!RoleHelper::is_admin() && !RoleHelper::is_super_admin() && !RoleHelper::is_team_lead() && !RoleHelper::is_admission_counsellor()) {
            abort(403, 'Access denied. Admin, Team Lead, or Admission Counsellor access required.');
        }

        $course = Course::findOrFail($courseId);
        $fromDate = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        // Get leads for this course
        $leadsQuery = Lead::with([
            'leadStatus:id,title,color', 
            'leadSource:id,title', 
            'telecaller:id,name', 
            'course:id,title'
        ])
        ->where('course_id', $courseId)
        ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        // Apply role-based filtering
        $this->applyRoleBasedFilter($leadsQuery);

        $leads = $leadsQuery->orderBy('created_at', 'desc')->paginate(50);

        // Get lead statuses for filter
        $leadStatuses = LeadStatus::select('id', 'title')->get();

        return view('admin.reports.course-leads', compact(
            'course', 'leads', 'leadStatuses', 'fromDate', 'toDate'
        ));
    }

    /**
     * Detailed Course Converted Leads Report
     * Shows all converted leads for a specific course
     */
    public function courseConvertedLeads(Request $request, $courseId)
    {
        // Check if user has access to reports
        if (!RoleHelper::is_admin() && !RoleHelper::is_super_admin() && !RoleHelper::is_team_lead() && !RoleHelper::is_admission_counsellor()) {
            abort(403, 'Access denied. Admin, Team Lead, or Admission Counsellor access required.');
        }

        $course = Course::findOrFail($courseId);
        $fromDate = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        // Get converted leads for this course
        $convertedLeadsQuery = ConvertedLead::with([
            'course:id,title',
            'academicAssistant:id,name'
        ])
        ->where('course_id', $courseId)
        ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        $convertedLeads = $convertedLeadsQuery->orderBy('created_at', 'desc')->paginate(50);

        return view('admin.reports.course-converted-leads', compact(
            'course', 'convertedLeads', 'fromDate', 'toDate'
        ));
    }

    /**
     * Export Course Summary to Excel
     */
    public function exportCourseSummaryExcel(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $courseId = $request->get('course_id', '');

        $courseSummary = $this->getCourseSummaryData($fromDate, $toDate, $courseId);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Course-wise Summary Report');
        $sheet->setCellValue('A2', 'Date Range: ' . $fromDate . ' to ' . $toDate);
        $sheet->setCellValue('A3', 'Generated: ' . now()->format('Y-m-d H:i:s'));

        // Set column headers
        $sheet->setCellValue('A5', 'Course Name');
        $sheet->setCellValue('B5', 'Total Leads');
        $sheet->setCellValue('C5', 'Converted Leads');
        $sheet->setCellValue('D5', 'Follow-up Leads');
        $sheet->setCellValue('E5', 'Conversion Rate (%)');
        $sheet->setCellValue('F5', 'Other Status Leads');

        // Add data
        $row = 6;
        foreach ($courseSummary as $course) {
            $sheet->setCellValue('A' . $row, $course['course_name']);
            $sheet->setCellValue('B' . $row, $course['total_leads']);
            $sheet->setCellValue('C' . $row, $course['converted_leads']);
            $sheet->setCellValue('D' . $row, $course['followup_leads']);
            $sheet->setCellValue('E' . $row, $course['conversion_rate']);
            $sheet->setCellValue('F' . $row, $course['other_leads']);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'course-summary-report-' . $fromDate . '-to-' . $toDate . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Export Course Summary to PDF
     */
    public function exportCourseSummaryPdf(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $courseId = $request->get('course_id', '');

        $courseSummary = $this->getCourseSummaryData($fromDate, $toDate, $courseId);

        $pdf = Pdf::loadView('admin.reports.exports.course-summary-pdf', [
            'courseSummary' => $courseSummary,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'generatedAt' => now()->format('Y-m-d H:i:s')
        ]);

        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('course-summary-report-' . $fromDate . '-to-' . $toDate . '.pdf');
    }

    /**
     * Get Course Summary Data
     */
    private function getCourseSummaryData($fromDate, $toDate, $courseId = '')
    {
        $query = Course::select('courses.id', 'courses.title as course_name')
            ->leftJoin('leads', 'courses.id', '=', 'leads.course_id')
            ->leftJoin('converted_leads', 'courses.id', '=', 'converted_leads.course_id')
            ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->orWhereBetween('converted_leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        if ($courseId) {
            $query->where('courses.id', $courseId);
        }

        // Apply role-based filtering for leads
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser && RoleHelper::is_team_lead()) {
            $query->whereHas('leads.telecaller', function($q) use ($currentUser) {
                $q->where('team_id', $currentUser->team_id);
            });
        }

        $courseData = $query->groupBy('courses.id', 'courses.title')
            ->selectRaw('
                courses.id,
                courses.title as course_name,
                COUNT(DISTINCT leads.id) as total_leads,
                COUNT(DISTINCT converted_leads.id) as converted_leads,
                COUNT(DISTINCT CASE WHEN leads.lead_status_id = 2 THEN leads.id END) as followup_leads,
                COUNT(DISTINCT CASE WHEN leads.lead_status_id NOT IN (2) AND leads.is_converted = 0 THEN leads.id END) as other_leads
            ')
            ->orderBy('courses.title')
            ->get();

        // Calculate conversion rates
        $courseData->transform(function ($course) {
            $conversionRate = $course->total_leads > 0 
                ? round(($course->converted_leads / $course->total_leads) * 100, 2) 
                : 0;
            
            return [
                'course_id' => $course->id,
                'course_name' => $course->course_name,
                'total_leads' => $course->total_leads,
                'converted_leads' => $course->converted_leads,
                'followup_leads' => $course->followup_leads,
                'other_leads' => $course->other_leads,
                'conversion_rate' => $conversionRate
            ];
        });

        return $courseData;
    }

    /**
     * Apply role-based filtering to queries
     */
    private function applyRoleBasedFilter($query)
    {
        $currentUser = AuthHelper::getCurrentUser();
        
        if ($currentUser && RoleHelper::is_team_lead()) {
            // Team Lead: Show only leads from their team
            $query->whereHas('telecaller', function($q) use ($currentUser) {
                $q->where('team_id', $currentUser->team_id);
            });
        } elseif ($currentUser && AuthHelper::isTelecaller()) {
            // Telecaller: Show only their own leads
            $query->where('telecaller_id', $currentUser->id);
        }
    }
}
