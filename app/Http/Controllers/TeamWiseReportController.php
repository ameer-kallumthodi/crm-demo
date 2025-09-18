<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\User;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\TelecallerActivityLog;
use App\Models\LeadStatus;
use App\Models\Course;
use App\Models\Country;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TeamWiseReportController extends Controller
{
    /**
     * Display team-wise detailed report
     */
    public function index(Request $request)
    {
        $teams = Team::with('teamLead')->get();
        $leadStatuses = LeadStatus::all();
        $courses = Course::all();
        $countries = Country::all();
        
        // Default date range (last 30 days)
        $fromDate = $request->get('from_date', now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));
        $teamId = $request->get('team_id');
        
        $reportData = $this->getTeamWiseReportData($fromDate, $toDate, $teamId);
        
        return view('admin.reports.team-wise', compact(
            'teams', 'leadStatuses', 'courses', 'countries', 
            'reportData', 'fromDate', 'toDate', 'teamId'
        ));
    }
    
    /**
     * Get team-wise report data
     */
    private function getTeamWiseReportData($fromDate, $toDate, $teamId = null)
    {
        $query = Team::with(['users', 'teamLead'])
            ->withCount(['users as total_members'])
            ->withCount(['users as active_members' => function($q) {
                $q->where('is_active', true);
            }]);
        
        if ($teamId) {
            $query->where('id', $teamId);
        }
        
        $teams = $query->get();
        
        $reportData = [];
        
        foreach ($teams as $team) {
            $teamData = $this->getTeamPerformanceData($team, $fromDate, $toDate);
            $reportData[] = $teamData;
        }
        
        return $reportData;
    }
    
    /**
     * Get team performance data
     */
    private function getTeamPerformanceData($team, $fromDate, $toDate)
    {
        $teamUsers = $team->users()->where('is_active', true)->get();
        $userIds = $teamUsers->pluck('id')->toArray();
        
        // Basic team metrics
        $totalLeads = Lead::whereIn('telecaller_id', $userIds)
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->count();
            
        $convertedLeads = Lead::whereIn('telecaller_id', $userIds)
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->where('is_converted', true)
            ->count();
            
        $conversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 2) : 0;
        
        // Get individual telecaller performance
        $telecallerPerformance = [];
        foreach ($teamUsers as $user) {
            $userPerformance = $this->getTelecallerPerformance($user, $fromDate, $toDate);
            $telecallerPerformance[] = $userPerformance;
        }
        
        // Segment analysis
        $segments = $this->getTeamSegments($teamUsers, $fromDate, $toDate);
        
        // Time-based analysis
        $timeAnalysis = empty($userIds) ? [
            'morning' => ['leads' => 0, 'conversions' => 0, 'conversion_rate' => 0],
            'evening' => ['leads' => 0, 'conversions' => 0, 'conversion_rate' => 0]
        ] : $this->getTimeBasedAnalysis($userIds, $fromDate, $toDate);
        
        // Product/Region analysis
        $productRegionAnalysis = empty($userIds) ? ['products' => collect(), 'regions' => collect()] : $this->getProductRegionAnalysis($userIds, $fromDate, $toDate);
        
        return [
            'team' => $team,
            'total_leads' => $totalLeads,
            'converted_leads' => $convertedLeads,
            'conversion_rate' => $conversionRate,
            'telecaller_performance' => $telecallerPerformance,
            'segments' => $segments,
            'time_analysis' => $timeAnalysis,
            'product_region_analysis' => $productRegionAnalysis,
        ];
    }
    
    /**
     * Get individual telecaller performance
     */
    private function getTelecallerPerformance($user, $fromDate, $toDate)
    {
        $totalLeads = Lead::where('telecaller_id', $user->id)
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->count();
            
        $convertedLeads = Lead::where('telecaller_id', $user->id)
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->where('is_converted', true)
            ->count();
            
        $conversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 2) : 0;
        
        // Calculate experience level
        $joiningDate = $user->joining_date ?? $user->created_at;
        $experienceDays = $joiningDate ? abs(round(now()->diffInDays($joiningDate))) : 0;
        $experienceLevel = $experienceDays < 30 ? 'New Joiner' : ($experienceDays < 180 ? 'Intermediate' : 'Experienced');
        
        // Get activity metrics
        $totalCalls = TelecallerActivityLog::where('user_id', $user->id)
            ->where('activity_type', 'call')
            ->whereBetween('activity_time', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->count();
            
        $avgCallDuration = TelecallerActivityLog::where('user_id', $user->id)
            ->where('activity_type', 'call')
            ->whereBetween('activity_time', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->avg('metadata->duration') ?? 0;
        
        return [
            'user' => $user,
            'total_leads' => $totalLeads,
            'converted_leads' => $convertedLeads,
            'conversion_rate' => $conversionRate,
            'experience_level' => $experienceLevel,
            'experience_days' => $experienceDays,
            'total_calls' => $totalCalls,
            'avg_call_duration' => round($avgCallDuration, 2),
        ];
    }
    
    /**
     * Get team segments analysis
     */
    private function getTeamSegments($teamUsers, $fromDate, $toDate)
    {
        $segments = [];
        
        // Top Performers vs Underperformers
        $performanceData = [];
        foreach ($teamUsers as $user) {
            $performance = $this->getTelecallerPerformance($user, $fromDate, $toDate);
            $performanceData[] = $performance;
        }
        
        // Filter out users with no leads or conversions
        $activePerformanceData = collect($performanceData)->filter(function($perf) {
            return $perf['total_leads'] > 0 || $perf['converted_leads'] > 0;
        });
        
        // Sort by conversion rate
        $sortedPerformance = $activePerformanceData->sortByDesc('conversion_rate');
        $totalUsers = $sortedPerformance->count();
        $topPerformersCount = ceil($totalUsers * 0.3); // Top 30%
        
        $segments['top_performers'] = $sortedPerformance->take($topPerformersCount)->values();
        $segments['underperformers'] = $sortedPerformance->skip($topPerformersCount)->values();
        
        // New Joiners vs Experienced (using filtered data)
        $newJoiners = [];
        $experienced = [];
        
        foreach ($activePerformanceData as $perf) {
            if ($perf['experience_days'] < 90) {
                $newJoiners[] = $perf;
            } else {
                $experienced[] = $perf;
            }
        }
        
        $segments['new_joiners'] = $newJoiners;
        $segments['experienced'] = $experienced;
        
        return $segments;
    }
    
    /**
     * Get time-based analysis
     */
    private function getTimeBasedAnalysis($userIds, $fromDate, $toDate)
    {
        $morningLeads = Lead::whereIn('telecaller_id', $userIds)
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->whereRaw('HOUR(created_at) BETWEEN 6 AND 12')
            ->count();
            
        $eveningLeads = Lead::whereIn('telecaller_id', $userIds)
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->whereRaw('HOUR(created_at) BETWEEN 18 AND 23')
            ->count();
            
        $morningConversions = Lead::whereIn('telecaller_id', $userIds)
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->whereRaw('HOUR(created_at) BETWEEN 6 AND 12')
            ->where('is_converted', true)
            ->count();
            
        $eveningConversions = Lead::whereIn('telecaller_id', $userIds)
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->whereRaw('HOUR(created_at) BETWEEN 18 AND 23')
            ->where('is_converted', true)
            ->count();
        
        return [
            'morning' => [
                'leads' => $morningLeads ?? 0,
                'conversions' => $morningConversions ?? 0,
                'conversion_rate' => ($morningLeads ?? 0) > 0 ? round((($morningConversions ?? 0) / ($morningLeads ?? 1)) * 100, 2) : 0,
            ],
            'evening' => [
                'leads' => $eveningLeads ?? 0,
                'conversions' => $eveningConversions ?? 0,
                'conversion_rate' => ($eveningLeads ?? 0) > 0 ? round((($eveningConversions ?? 0) / ($eveningLeads ?? 1)) * 100, 2) : 0,
            ],
        ];
    }
    
    /**
     * Get product/region analysis
     */
    private function getProductRegionAnalysis($userIds, $fromDate, $toDate)
    {
        try {
            // Product-wise analysis
            $productAnalysis = Lead::whereIn('telecaller_id', $userIds)
                ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                ->whereNotNull('course_id')
                ->join('courses', 'leads.course_id', '=', 'courses.id')
                ->select('courses.title as course_name', 
                    DB::raw('COUNT(leads.id) as total_leads'),
                    DB::raw('SUM(CASE WHEN leads.is_converted = 1 THEN 1 ELSE 0 END) as conversions')
                )
                ->groupBy('courses.id', 'courses.title')
                ->get();
        } catch (\Exception $e) {
            $productAnalysis = collect();
        }
        
        try {
            // Region-wise analysis
            $regionAnalysis = Lead::whereIn('telecaller_id', $userIds)
                ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                ->whereNotNull('country_id')
                ->join('countries', 'leads.country_id', '=', 'countries.id')
                ->select('countries.title as country_name',
                    DB::raw('COUNT(leads.id) as total_leads'),
                    DB::raw('SUM(CASE WHEN leads.is_converted = 1 THEN 1 ELSE 0 END) as conversions')
                )
                ->groupBy('countries.id', 'countries.title')
                ->get();
        } catch (\Exception $e) {
            $regionAnalysis = collect();
        }
        
        return [
            'products' => $productAnalysis,
            'regions' => $regionAnalysis,
        ];
    }
    
    /**
     * Display detailed single team report
     */
    public function teamDetail(Request $request)
    {
        $teamId = $request->get('team_id');
        $fromDate = $request->get('from_date', now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));
        
        if (!$teamId) {
            return redirect()->route('admin.reports.team-wise')->with('error', 'Team ID is required');
        }
        
        $team = Team::with(['users', 'teamLead'])->findOrFail($teamId);
        $leadStatuses = LeadStatus::all();
        $courses = Course::all();
        $countries = Country::all();
        
        // Get detailed team data
        $teamData = $this->getTeamPerformanceData($team, $fromDate, $toDate);
        
        // Get additional detailed metrics
        $detailedMetrics = $this->getDetailedTeamMetrics($team, $fromDate, $toDate);
        
        return view('admin.reports.team-wise-detail', compact(
            'team', 'teamData', 'detailedMetrics', 'leadStatuses', 'courses', 'countries',
            'fromDate', 'toDate'
        ));
    }
    
    /**
     * Get detailed team metrics
     */
    private function getDetailedTeamMetrics($team, $fromDate, $toDate)
    {
        $teamUsers = $team->users()->where('is_active', true)->get();
        $userIds = $teamUsers->pluck('id')->toArray();
        
        // Daily performance trends
        $dailyTrends = Lead::whereIn('telecaller_id', $userIds)
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total_leads, SUM(CASE WHEN is_converted = 1 THEN 1 ELSE 0 END) as conversions')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Lead source performance
        $leadSourcePerformance = Lead::whereIn('telecaller_id', $userIds)
            ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->join('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
            ->select('lead_sources.title as source_name',
                DB::raw('COUNT(leads.id) as total_leads'),
                DB::raw('SUM(CASE WHEN leads.is_converted = 1 THEN 1 ELSE 0 END) as conversions')
            )
            ->groupBy('lead_sources.id', 'lead_sources.title')
            ->get();
        
        // Lead status distribution
        $leadStatusDistribution = Lead::whereIn('telecaller_id', $userIds)
            ->whereBetween('leads.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->join('lead_statuses', 'leads.lead_status_id', '=', 'lead_statuses.id')
            ->select('lead_statuses.title as status_name',
                DB::raw('COUNT(leads.id) as count')
            )
            ->groupBy('lead_statuses.id', 'lead_statuses.title')
            ->get();
        
        // Call activity analysis
        $callActivity = TelecallerActivityLog::whereIn('user_id', $userIds)
            ->whereBetween('activity_time', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->where('activity_type', 'call')
            ->selectRaw('user_id, COUNT(*) as total_calls, AVG(JSON_EXTRACT(metadata, "$.duration")) as avg_duration')
            ->groupBy('user_id')
            ->get();
        
        return [
            'daily_trends' => $dailyTrends,
            'lead_source_performance' => $leadSourcePerformance,
            'lead_status_distribution' => $leadStatusDistribution,
            'call_activity' => $callActivity,
        ];
    }
    
    /**
     * Export team-wise report to Excel
     */
    public function export(Request $request)
    {
        $fromDate = $request->get('from_date', now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));
        $teamId = $request->get('team_id');
        
        $reportData = $this->getTeamWiseReportData($fromDate, $toDate, $teamId);
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\TeamWiseReportExport($reportData, $fromDate, $toDate),
            'team-wise-report-' . $fromDate . '-to-' . $toDate . '.xlsx'
        );
    }
    
    /**
     * Export team-wise report to PDF
     */
    public function exportPdf(Request $request)
    {
        $fromDate = $request->get('from_date', now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));
        $teamId = $request->get('team_id');
        
        $reportData = $this->getTeamWiseReportData($fromDate, $toDate, $teamId);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.pdf.team-wise', [
            'reportData' => $reportData,
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ]);
        
        return $pdf->download('team-wise-report-' . $fromDate . '-to-' . $toDate . '.pdf');
    }
}
