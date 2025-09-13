<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\LeadSource;
use App\Models\Team;
use App\Models\User;
use App\Models\Country;
use App\Models\Course;
use App\Helpers\AuthHelper;
use Carbon\Carbon;

class LeadReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    public function index(Request $request)
    {
        // Default date range (last 7 days)
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        // Get filter options
        $leadStatuses = LeadStatus::select('id', 'title', 'color')->get();
        $leadSources = LeadSource::select('id', 'title')->get();
        $teams = Team::select('id', 'name as title')->get();
        
        // Get reports data
        $reports = [
            'lead_status' => $this->getLeadStatusReport($fromDate, $toDate),
            'lead_source' => $this->getLeadSourceReport($fromDate, $toDate),
            'team' => $this->getTeamReport($fromDate, $toDate),
            'telecaller' => $this->getTelecallerReport($fromDate, $toDate),
        ];
        
        return view('admin.reports.leads', compact(
            'reports', 'leadStatuses', 'leadSources', 'teams', 'fromDate', 'toDate'
        ));
    }

    public function leadStatusReport(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $leadStatusId = $request->get('lead_status_id');
        
        // Get filter options
        $leadStatuses = LeadStatus::select('id', 'title', 'color')->get();
        
        // Get reports data
        $reports = [
            'lead_status' => $this->getLeadStatusReport($fromDate, $toDate),
            'monthly' => $this->getMonthlyReport($fromDate, $toDate),
            'conversion' => $this->getConversionReport($fromDate, $toDate),
        ];
        
        // Get leads data for the detailed view with optional lead status filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            
        if ($leadStatusId) {
            $leadsQuery->where('lead_status_id', $leadStatusId);
        }
        
        $leads = $leadsQuery->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.reports.lead-status', compact('reports', 'leads', 'fromDate', 'toDate', 'leadStatuses', 'leadStatusId'));
    }

    public function leadSourceReport(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $leadSourceId = $request->get('lead_source_id');
        
        // Get filter options
        $leadSources = LeadSource::select('id', 'title')->get();
        
        // Get reports data
        $reports = [
            'lead_source' => $this->getLeadSourceReport($fromDate, $toDate),
            'monthly' => $this->getMonthlyReport($fromDate, $toDate),
        ];
        
        // Get leads data for the detailed view with optional lead source filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            
        if ($leadSourceId) {
            $leadsQuery->where('lead_source_id', $leadSourceId);
        }
        
        $leads = $leadsQuery->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.reports.lead-source', compact('reports', 'leads', 'fromDate', 'toDate', 'leadSources', 'leadSourceId'));
    }

    public function teamReport(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $teamId = $request->get('team_id');
        
        // Get filter options
        $teams = Team::select('id', 'name')->get();
        
        // Get reports data
        $reports = [
            'team' => $this->getTeamReport($fromDate, $toDate),
            'monthly' => $this->getMonthlyReport($fromDate, $toDate),
        ];
        
        // Get leads data for the detailed view with optional team filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name', 'team:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            
        if ($teamId) {
            $leadsQuery->where('team_id', $teamId);
        }
        
        $leads = $leadsQuery->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.reports.team', compact('reports', 'leads', 'fromDate', 'toDate', 'teams', 'teamId'));
    }

    public function telecallerReport(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $telecallerId = $request->get('telecaller_id');
        $teamId = $request->get('team_id');
        
        // Get filter options
        $telecallersQuery = User::where('role_id', 3)->select('id', 'name', 'phone');
        if ($teamId) {
            $telecallersQuery->where('team_id', $teamId);
        }
        $telecallers = $telecallersQuery->get();
        
        // Get reports data
        $reports = [
            'telecaller' => $this->getTelecallerReport($fromDate, $toDate, $teamId),
            'monthly' => $this->getMonthlyReport($fromDate, $toDate),
        ];
        
        // Get leads data for the detailed view with optional telecaller filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name', 'team:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            
        if ($telecallerId) {
            $leadsQuery->where('telecaller_id', $telecallerId);
        }
        
        if ($teamId) {
            $leadsQuery->where('team_id', $teamId);
        }
        
        $leads = $leadsQuery->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.reports.telecaller', compact('reports', 'leads', 'fromDate', 'toDate', 'telecallers', 'telecallerId', 'teamId'));
    }

    private function getLeadStatusReport($fromDate, $toDate)
    {
        return Lead::select('lead_statuses.title', 'lead_statuses.color')
            ->selectRaw('COUNT(leads.id) as count')
            ->join('lead_statuses', 'leads.lead_status_id', '=', 'lead_statuses.id')
            ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('lead_statuses.id', 'lead_statuses.title', 'lead_statuses.color')
            ->orderBy('count', 'desc')
            ->get();
    }

    private function getLeadSourceReport($fromDate, $toDate)
    {
        return Lead::select('lead_sources.title')
            ->selectRaw('COUNT(leads.id) as count')
            ->join('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
            ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('lead_sources.id', 'lead_sources.title')
            ->orderBy('count', 'desc')
            ->get();
    }

    private function getTeamReport($fromDate, $toDate)
    {
        // Get all teams first
        $allTeams = Team::select('id', 'name as title')->get();
        
        $teams = collect();
        
        foreach ($allTeams as $team) {
            // Get lead count for this team
            $leadCount = Lead::where('team_id', $team->id)
                ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                ->count();
            
            // Get telecaller data for this team
            $telecallers = Lead::select('users.id', 'users.name')
                ->selectRaw('COUNT(leads.id) as lead_count')
                ->join('users', 'leads.telecaller_id', '=', 'users.id')
                ->where('leads.team_id', $team->id)
                ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                ->where('users.role_id', 3) // Telecaller role
                ->groupBy('users.id', 'users.name')
                ->orderBy('lead_count', 'desc')
                ->get();
            
            $team->count = $leadCount;
            $team->telecallers = $telecallers;
            
            $teams->push($team);
        }
        
        // Sort by lead count descending
        return $teams->sortByDesc('count')->values();
    }

    private function getTelecallerReport($fromDate, $toDate, $teamId = null)
    {
        // First, get all telecallers (users with role_id = 3)
        $telecallersQuery = User::where('role_id', 3)
            ->select('id', 'name', 'phone', 'team_id');
            
        if ($teamId) {
            $telecallersQuery->where('team_id', $teamId);
        }
        
        $allTelecallers = $telecallersQuery->get();
        
        $telecallers = collect();
        
        foreach ($allTelecallers as $telecaller) {
            // Get lead count for this telecaller in the date range
            $leadCount = Lead::where('telecaller_id', $telecaller->id)
                ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                ->count();
            
            // Get team name for this telecaller
            $team = Team::where('id', $telecaller->team_id)->first();
            $teamName = $team ? $team->name : null;
            
            // Create telecaller object with count
            $telecallerData = (object) [
                'id' => $telecaller->id,
                'name' => $telecaller->name,
                'phone' => $telecaller->phone,
                'team_name' => $teamName,
                'count' => $leadCount
            ];
            
            $telecallers->push($telecallerData);
        }
        
        // Sort by lead count descending and return
        return $telecallers->sortByDesc('count')->values();
    }

    private function getCountryReport($fromDate, $toDate)
    {
        return Lead::select('countries.title')
            ->selectRaw('COUNT(leads.id) as count')
            ->join('countries', 'leads.country_id', '=', 'countries.id')
            ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('countries.id', 'countries.title')
            ->orderBy('count', 'desc')
            ->get();
    }

    private function getCourseReport($fromDate, $toDate)
    {
        return Lead::select('courses.title')
            ->selectRaw('COUNT(leads.id) as count')
            ->join('courses', 'leads.course_id', '=', 'courses.id')
            ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('courses.id', 'courses.title')
            ->orderBy('count', 'desc')
            ->get();
    }


    private function getMonthlyReport($fromDate, $toDate)
    {
        $months = [];
        $startDate = Carbon::parse($fromDate);
        $endDate = Carbon::parse($toDate);
        
        // Generate all months in the range
        while ($startDate->lte($endDate)) {
            $monthKey = $startDate->format('Y-m');
            $monthName = $startDate->format('M Y');
            
            // Get total leads for this month
            $totalLeads = Lead::whereYear('created_at', $startDate->year)
                ->whereMonth('created_at', $startDate->month)
                ->count();
            
            // Get converted leads for this month
            $convertedLeads = Lead::whereYear('created_at', $startDate->year)
                ->whereMonth('created_at', $startDate->month)
                ->where('is_converted', true)
                ->count();
            
            // Calculate conversion rate
            $conversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 2) : 0;
            
            $months[] = (object) [
                'month' => $monthName,
                'count' => $totalLeads,
                'total_leads' => $totalLeads,
                'converted' => $convertedLeads,
                'conversion_rate' => $conversionRate
            ];
            
            $startDate->addMonth();
        }
        
        return collect($months);
    }

    private function getConversionReport($fromDate, $toDate)
    {
        $totalLeads = Lead::whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])->count();
        $convertedLeads = Lead::whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->where('is_converted', true)
            ->count();
        
        return [
            'total_leads' => $totalLeads,
            'converted_leads' => $convertedLeads,
            'conversion_rate' => $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 2) : 0
        ];
    }

    /**
     * Export Lead Status Report to Excel
     */
    public function exportLeadStatusExcel(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $leadStatusId = $request->get('lead_status_id');
        
        // Get leads data for the detailed view with optional lead status filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            
        if ($leadStatusId) {
            $leadsQuery->where('lead_status_id', $leadStatusId);
        }
        
        $leads = $leadsQuery->orderBy('created_at', 'desc')->get();
        
        $export = new \App\Exports\LeadStatusReportExport($leads, $fromDate, $toDate);
        $spreadsheet = $export->export();
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $filename = 'lead_status_report_' . $fromDate . '_to_' . $toDate . '.xlsx';
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Export Lead Status Report to PDF
     */
    public function exportLeadStatusPdf(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $leadStatusId = $request->get('lead_status_id');
        
        // Get leads data for the detailed view with optional lead status filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            
        if ($leadStatusId) {
            $leadsQuery->where('lead_status_id', $leadStatusId);
        }
        
        $leads = $leadsQuery->orderBy('created_at', 'desc')->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.exports.lead-status-pdf', [
            'leads' => $leads,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'reportType' => 'Lead Status Report',
            'generatedAt' => now()->format('Y-m-d H:i:s')
        ]);
        
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('lead_status_report_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

    /**
     * Export Lead Source Report to Excel
     */
    public function exportLeadSourceExcel(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $leadSourceId = $request->get('lead_source_id');
        
        // Get leads data for the detailed view with optional lead source filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            
        if ($leadSourceId) {
            $leadsQuery->where('lead_source_id', $leadSourceId);
        }
        
        $leads = $leadsQuery->orderBy('created_at', 'desc')->get();
        
        $export = new \App\Exports\LeadSourceReportExport($leads, $fromDate, $toDate);
        $spreadsheet = $export->export();
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $filename = 'lead_source_report_' . $fromDate . '_to_' . $toDate . '.xlsx';
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Export Lead Source Report to PDF
     */
    public function exportLeadSourcePdf(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $leadSourceId = $request->get('lead_source_id');
        
        // Get leads data for the detailed view with optional lead source filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            
        if ($leadSourceId) {
            $leadsQuery->where('lead_source_id', $leadSourceId);
        }
        
        $leads = $leadsQuery->orderBy('created_at', 'desc')->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.exports.lead-source-pdf', [
            'leads' => $leads,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'reportType' => 'Lead Source Report',
            'generatedAt' => now()->format('Y-m-d H:i:s')
        ]);
        
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('lead_source_report_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

    /**
     * Export Team Report to Excel
     */
    public function exportTeamExcel(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $teamId = $request->get('team_id');
        
        // Get leads data for the detailed view with optional team filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name', 'team:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            
        if ($teamId) {
            $leadsQuery->where('team_id', $teamId);
        }
        
        $leads = $leadsQuery->orderBy('created_at', 'desc')->get();
        
        $export = new \App\Exports\TeamReportExport($leads, $fromDate, $toDate);
        $spreadsheet = $export->export();
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $filename = 'team_report_' . $fromDate . '_to_' . $toDate . '.xlsx';
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Export Team Report to PDF
     */
    public function exportTeamPdf(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $teamId = $request->get('team_id');
        
        // Get leads data for the detailed view with optional team filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name', 'team:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            
        if ($teamId) {
            $leadsQuery->where('team_id', $teamId);
        }
        
        $leads = $leadsQuery->orderBy('created_at', 'desc')->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.exports.team-pdf', [
            'leads' => $leads,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'reportType' => 'Team Report',
            'generatedAt' => now()->format('Y-m-d H:i:s')
        ]);
        
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('team_report_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

    /**
     * Export Telecaller Report to Excel
     */
    public function exportTelecallerExcel(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $telecallerId = $request->get('telecaller_id');
        $teamId = $request->get('team_id');
        
        // Get telecaller report data
        $reports = [
            'telecaller' => $this->getTelecallerReport($fromDate, $toDate, $teamId),
        ];
        
        // Get leads data for the detailed view with optional telecaller filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            
        if ($telecallerId) {
            $leadsQuery->where('telecaller_id', $telecallerId);
        }
        
        if ($teamId) {
            $leadsQuery->where('team_id', $teamId);
        }
        
        $leads = $leadsQuery->orderBy('created_at', 'desc')->get();
        
        $export = new \App\Exports\TelecallerReportExport($reports, $fromDate, $toDate);
        $spreadsheet = $export->export();
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $filename = 'telecaller_report_' . $fromDate . '_to_' . $toDate . '.xlsx';
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Export Telecaller Report to PDF
     */
    public function exportTelecallerPdf(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $telecallerId = $request->get('telecaller_id');
        $teamId = $request->get('team_id');
        
        // Get telecaller report data
        $reports = [
            'telecaller' => $this->getTelecallerReport($fromDate, $toDate, $teamId),
        ];
        
        // Get leads data for the detailed view with optional telecaller filter
        $leadsQuery = Lead::with(['leadStatus:id,title,color', 'leadSource:id,title', 'telecaller:id,name'])
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            
        if ($telecallerId) {
            $leadsQuery->where('telecaller_id', $telecallerId);
        }
        
        if ($teamId) {
            $leadsQuery->where('team_id', $teamId);
        }
        
        $leads = $leadsQuery->orderBy('created_at', 'desc')->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.exports.telecaller-pdf', [
            'reports' => $reports,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'reportType' => 'Telecaller Report',
            'generatedAt' => now()->format('Y-m-d H:i:s')
        ]);
        
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('telecaller_report_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

    /**
     * Export Main Reports to Excel
     */
    public function exportMainReportsExcel(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        // Get reports data
        $reports = [
            'lead_status' => $this->getLeadStatusReport($fromDate, $toDate),
            'lead_source' => $this->getLeadSourceReport($fromDate, $toDate),
            'team' => $this->getTeamReport($fromDate, $toDate),
            'telecaller' => $this->getTelecallerReport($fromDate, $toDate),
        ];
        
        $export = new \App\Exports\MainReportsExport($reports, $fromDate, $toDate);
        $spreadsheet = $export->export();
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $filename = 'main_reports_' . $fromDate . '_to_' . $toDate . '.xlsx';
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Export Main Reports to PDF
     */
    public function exportMainReportsPdf(Request $request)
    {
        $fromDate = $request->get('date_from', Carbon::now()->subDays(7)->format('Y-m-d'));
        $toDate = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        
        // Get reports data
        $reports = [
            'lead_status' => $this->getLeadStatusReport($fromDate, $toDate),
            'lead_source' => $this->getLeadSourceReport($fromDate, $toDate),
            'team' => $this->getTeamReport($fromDate, $toDate),
            'telecaller' => $this->getTelecallerReport($fromDate, $toDate),
        ];
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.exports.main-reports-pdf', [
            'reports' => $reports,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'reportType' => 'Main Reports',
            'generatedAt' => now()->format('Y-m-d H:i:s')
        ]);
        
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('main_reports_' . $fromDate . '_to_' . $toDate . '.pdf');
    }
}