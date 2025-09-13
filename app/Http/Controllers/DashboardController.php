<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\User;
use App\Models\LeadStatus;
use App\Models\Country;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $data = [
            'leadStatuses' => $this->getLeadStatusesWithCount(),
            'topTelecallers' => $this->getTopTelecallers(),
            'topCountries' => $this->getTopCountries(),
            'totalLeads' => $this->getTotalLeadsCount(),
            'totalUsers' => User::whereNotIn('role_id', [1, 2])->count(),
            'totalAdmins' => User::where('role_id', 2)->count(),
            'totalTelecallers' => User::where('role_id', 6)->count(),
            'recentLeads' => $this->getRecentLeads(),
            'monthlyLeads' => $this->getMonthlyLeadsData(),
            'leadSourcesData' => $this->getLeadSourcesData(),
            'conversionRate' => $this->getConversionRate(),
            'recentActivities' => $this->getRecentActivities(),
            'weeklyStats' => $this->getWeeklyStats(),
        ];

        return view('dashboard', $data);
    }

    /**
     * Get top telecallers by lead count.
     */
    private function getTopTelecallers()
    {
        // Optimized query to get telecallers with lead counts in a single query
        $telecallers = User::select('users.id', 'users.name', 'users.phone', 'users.profile_picture')
            ->selectRaw('COUNT(leads.id) as lead_count')
            ->leftJoin('leads', 'users.id', '=', 'leads.telecaller_id')
            ->where('users.role_id', 6)
            ->whereNull('users.deleted_at')
            ->whereNull('leads.deleted_at')
            ->groupBy('users.id', 'users.name', 'users.phone', 'users.profile_picture')
            ->having('lead_count', '>', 0)
            ->orderByDesc('lead_count')
            ->limit(5)
            ->get()
            ->map(function ($telecaller) {
                return [
                    'id' => $telecaller->id,
                    'name' => $telecaller->name,
                    'phone' => $telecaller->phone,
                    'profile_picture' => $telecaller->profile_picture,
                    'count' => $telecaller->lead_count,
                ];
            });

        return $telecallers;
    }

    /**
     * Get top countries by lead count.
     */
    private function getTopCountries()
    {
        // Optimized query to get countries with lead counts in a single query
        $countries = Country::select('countries.id', 'countries.title')
            ->selectRaw('COUNT(leads.id) as lead_count')
            ->leftJoin('leads', 'countries.id', '=', 'leads.country_id')
            ->whereNull('countries.deleted_at')
            ->whereNull('leads.deleted_at')
            ->groupBy('countries.id', 'countries.title')
            ->having('lead_count', '>', 0)
            ->orderByDesc('lead_count')
            ->limit(5)
            ->get()
            ->map(function ($country) {
                return [
                    'id' => $country->id,
                    'title' => $country->title,
                    'count' => $country->lead_count,
                ];
            });

        return $countries;
    }

    /**
     * Get monthly leads data for charts.
     */
    private function getMonthlyLeadsData()
    {
        $months = [];
        $leadCounts = [];
        $convertedCounts = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            
            $totalLeadsQuery = Lead::whereBetween('created_at', [$monthStart, $monthEnd]);
            $convertedLeadsQuery = Lead::whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('is_converted', true);
            
            $totalLeads = $this->applyRoleBasedFilter($totalLeadsQuery)->count();
            $convertedLeads = $this->applyRoleBasedFilter($convertedLeadsQuery)->count();
            
            $months[] = $date->format('M Y');
            $leadCounts[] = $totalLeads;
            $convertedCounts[] = $convertedLeads;
        }
        
        return [
            'months' => $months,
            'leadCounts' => $leadCounts,
            'convertedCounts' => $convertedCounts,
        ];
    }

    /**
     * Get lead sources data for charts.
     */
    private function getLeadSourcesData()
    {
        $query = Lead::select('lead_sources.title')
            ->selectRaw('COUNT(leads.id) as count')
            ->join('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
            ->whereNull('leads.deleted_at')
            ->whereNull('lead_sources.deleted_at')
            ->groupBy('lead_sources.id', 'lead_sources.title')
            ->orderByDesc('count')
            ->limit(5);
        
        return $this->applyRoleBasedFilter($query)->get()
            ->map(function ($source) {
                return [
                    'name' => $source->title,
                    'value' => $source->count,
                ];
            });
    }

    /**
     * Get conversion rate data.
     */
    private function getConversionRate()
    {
        $totalLeadsQuery = Lead::query();
        $convertedLeadsQuery = Lead::where('is_converted', true);
        
        $totalLeads = $this->applyRoleBasedFilter($totalLeadsQuery)->count();
        $convertedLeads = $this->applyRoleBasedFilter($convertedLeadsQuery)->count();
        
        return $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 2) : 0;
    }

    /**
     * Get recent activities.
     */
    private function getRecentActivities()
    {
        $activities = [];
        
        // Recent leads
        $recentLeadsQuery = Lead::with('leadStatus')
            ->orderBy('created_at', 'desc')
            ->limit(5);
        
        $recentLeads = $this->applyRoleBasedFilter($recentLeadsQuery)->get();
            
        foreach ($recentLeads as $lead) {
            $activities[] = [
                'type' => 'lead_added',
                'title' => 'New Lead Added',
                'description' => $lead->title,
                'time' => $lead->created_at,
                'icon' => 'ti ti-user-plus',
                'color' => 'success',
            ];
        }
        
        // Recent conversions
        $recentConversionsQuery = Lead::with('leadStatus')
            ->where('is_converted', true)
            ->orderBy('updated_at', 'desc')
            ->limit(3);
        
        $recentConversions = $this->applyRoleBasedFilter($recentConversionsQuery)->get();
            
        foreach ($recentConversions as $lead) {
            $activities[] = [
                'type' => 'lead_converted',
                'title' => 'Lead Converted',
                'description' => $lead->title,
                'time' => $lead->updated_at,
                'icon' => 'ti ti-check-circle',
                'color' => 'primary',
            ];
        }
        
        // Sort by time and limit to 8 activities
        usort($activities, function($a, $b) {
            return $b['time']->timestamp - $a['time']->timestamp;
        });
        
        return array_slice($activities, 0, 8);
    }

    /**
     * Get weekly statistics.
     */
    private function getWeeklyStats()
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        
        $totalLeadsQuery = Lead::whereBetween('created_at', [$weekStart, $weekEnd]);
        $convertedLeadsQuery = Lead::whereBetween('created_at', [$weekStart, $weekEnd])
            ->where('is_converted', true);
        
        $totalLeads = $this->applyRoleBasedFilter($totalLeadsQuery)->count();
        $convertedLeads = $this->applyRoleBasedFilter($convertedLeadsQuery)->count();
        
        return [
            'totalLeads' => $totalLeads,
            'convertedLeads' => $convertedLeads,
            'conversionRate' => $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 2) : 0,
        ];
    }

    /**
     * Get lead statuses with count based on user role
     */
    private function getLeadStatusesWithCount()
    {
        // Get leads with their status counts, filtered by user role
        $leadsQuery = Lead::query();
        $filteredLeads = $this->applyRoleBasedFilter($leadsQuery)->get();
        
        // Group by lead status and count
        $statusCounts = $filteredLeads->groupBy('lead_status_id')->map(function ($leads) {
            return $leads->count();
        });
        
        // Get status details with counts
        $leadStatuses = LeadStatus::withCount(['leads' => function ($query) {
            $this->applyRoleBasedFilter($query);
        }])->get();
        
        return $leadStatuses;
    }

    /**
     * Get total leads count based on user role
     */
    private function getTotalLeadsCount()
    {
        $query = Lead::query();
        return $this->applyRoleBasedFilter($query)->count();
    }

    /**
     * Get recent leads based on user role
     */
    private function getRecentLeads()
    {
        $query = Lead::with(['leadStatus', 'leadSource'])
            ->orderBy('created_at', 'desc')
            ->limit(10);
        
        return $this->applyRoleBasedFilter($query)->get();
    }

    /**
     * Apply role-based filtering to lead queries
     */
    private function applyRoleBasedFilter($query)
    {
        $currentUser = \App\Helpers\AuthHelper::getCurrentUser();
        
        if ($currentUser) {
            if (\App\Helpers\AuthHelper::isTelecaller()) {
                // Telecaller: Can only see their own leads
                $query->where('telecaller_id', \App\Helpers\AuthHelper::getCurrentUserId());
            } elseif (\App\Helpers\AuthHelper::isTeamLead()) {
                // Team Lead: Can see their own leads + their team members' leads
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Helpers\AuthHelper::getTeamMemberIds($teamId);
                    // Include current user's ID in the team member IDs
                    $teamMemberIds[] = \App\Helpers\AuthHelper::getCurrentUserId();
                    $query->whereIn('telecaller_id', $teamMemberIds);
                } else {
                    // If no team assigned, only show their own leads
                    $query->where('telecaller_id', \App\Helpers\AuthHelper::getCurrentUserId());
                }
            }
        }
        
        return $query;
    }
}
