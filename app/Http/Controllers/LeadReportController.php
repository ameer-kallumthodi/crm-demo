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
        $teams = Lead::select('teams.id', 'teams.name as title')
            ->selectRaw('COUNT(leads.id) as count')
            ->join('teams', 'leads.team_id', '=', 'teams.id')
            ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->groupBy('teams.id', 'teams.name')
            ->orderBy('count', 'desc')
            ->get();

        // Add telecaller data for each team
        foreach ($teams as $team) {
            $telecallers = Lead::select('users.id', 'users.name')
                ->selectRaw('COUNT(leads.id) as lead_count')
                ->join('users', 'leads.telecaller_id', '=', 'users.id')
                ->where('leads.team_id', $team->id)
                ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                ->where('users.role_id', 6) // Telecaller role
                ->groupBy('users.id', 'users.name')
                ->orderBy('lead_count', 'desc')
                ->get();

            $team->telecallers = $telecallers;
        }

        return $teams;
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

    private function getTelecallerReport($fromDate, $toDate)
    {
        return Lead::select('users.name')
            ->selectRaw('COUNT(leads.id) as count')
            ->join('users', 'leads.telecaller_id', '=', 'users.id')
            ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->where('users.role_id', 6) // Telecaller role
            ->groupBy('users.id', 'users.name')
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
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LeadStatusReportExport($leads, $fromDate, $toDate),
            'lead_status_report_' . $fromDate . '_to_' . $toDate . '.xlsx'
        );
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
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LeadSourceReportExport($leads, $fromDate, $toDate),
            'lead_source_report_' . $fromDate . '_to_' . $toDate . '.xlsx'
        );
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
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\TeamReportExport($leads, $fromDate, $toDate),
            'team_report_' . $fromDate . '_to_' . $toDate . '.xlsx'
        );
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
}