<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TelecallerSession;
use App\Models\TelecallerIdleTime;
use App\Models\TelecallerActivityLog;
use App\Models\Lead;
use App\Models\User;
use App\Helpers\AuthHelper;
use App\Exports\TelecallerReportExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TelecallerReportController extends Controller
{
    /**
     * Constructor - Check if user is super admin
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!AuthHelper::isSuperAdmin()) {
                abort(403, 'Access denied. Super admin access required.');
            }
            return $next($request);
        });
    }

    /**
     * Dashboard view
     */
    public function dashboard()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        // Get telecallers
        $telecallers = User::where('role_id', 3)->get();

        // Today's statistics
        $todayStats = $this->getDateRangeStats($today, $today->copy()->endOfDay());
        
        // This week's statistics
        $weekStats = $this->getDateRangeStats($thisWeek, Carbon::now());
        
        // This month's statistics
        $monthStats = $this->getDateRangeStats($thisMonth, Carbon::now());

        // Recent activities
        $recentActivities = TelecallerActivityLog::with('user')
            ->orderBy('activity_time', 'desc')
            ->limit(10)
            ->get();

        // Overdue leads (tasks) - leads assigned but not converted
        $overdueTasks = Lead::with('telecaller')
            ->whereNotNull('telecaller_id')
            ->where('is_converted', false)
            ->where('created_at', '<', now()->subDays(7)) // Consider leads older than 7 days as overdue
            ->get();

        return view('admin.telecaller-tracking.dashboard', compact(
            'telecallers',
            'todayStats',
            'weekStats', 
            'monthStats',
            'recentActivities',
            'overdueTasks'
        ));
    }

    /**
     * Detailed reports view
     */
    public function reports(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $telecallerId = $request->get('telecaller_id');

        $query = TelecallerSession::with(['user', 'idleTimes', 'activityLogs'])
            ->whereBetween('login_time', [$startDate, $endDate]);

        if ($telecallerId) {
            $query->where('user_id', $telecallerId);
        }

        $sessions = $query->orderBy('login_time', 'desc')->get();

        $telecallers = User::where('role_id', 3)->get();

        return view('admin.telecaller-tracking.reports', compact(
            'sessions',
            'telecallers',
            'startDate',
            'endDate',
            'telecallerId'
        ));
    }

    /**
     * Individual telecaller report
     */
    public function telecallerReport($userId, Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $telecaller = User::findOrFail($userId);
        
        $sessions = TelecallerSession::where('user_id', $userId)
            ->whereBetween('login_time', [$startDate, $endDate])
            ->with(['idleTimes', 'activityLogs'])
            ->orderBy('login_time', 'desc')
            ->get();

        $tasks = Lead::where('telecaller_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = $this->getTelecallerStats($userId, $startDate, $endDate);

        return view('admin.telecaller-tracking.telecaller-report', compact(
            'telecaller',
            'sessions',
            'tasks',
            'stats',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export reports to Excel
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $telecallerId = $request->get('telecaller_id');

        $fileName = 'telecaller-report-' . $startDate . '-to-' . $endDate . '.xlsx';
        
        return Excel::download(
            new TelecallerReportExport($startDate, $endDate, $telecallerId),
            $fileName
        );
    }

    /**
     * Export reports to PDF
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $telecallerId = $request->get('telecaller_id');

        // Get data for PDF
        $query = TelecallerSession::with(['user', 'idleTimes'])
            ->whereBetween('login_time', [$startDate, $endDate]);

        if ($telecallerId) {
            $query->where('user_id', $telecallerId);
        }

        $sessions = $query->orderBy('login_time', 'desc')->get();
        
        // Generate PDF using a simple HTML view
        $pdf = \PDF::loadView('admin.telecaller-tracking.pdf-report', [
            'sessions' => $sessions,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'telecallerId' => $telecallerId
        ]);

        $fileName = 'telecaller-report-' . $startDate . '-to-' . $endDate . '.pdf';
        
        return $pdf->download($fileName);
    }

    /**
     * Get statistics for a date range
     */
    private function getDateRangeStats($startDate, $endDate)
    {
        $sessions = TelecallerSession::whereBetween('login_time', [$startDate, $endDate]);
        $idleTimes = TelecallerIdleTime::whereBetween('idle_start_time', [$startDate, $endDate]);
        $tasks = Lead::whereBetween('created_at', [$startDate, $endDate])->whereNotNull('telecaller_id');

        return [
            'total_sessions' => $sessions->count(),
            'total_login_hours' => $sessions->sum('total_duration_minutes') / 60,
            'total_idle_hours' => $idleTimes->sum('idle_duration_seconds') / 3600,
            'total_active_hours' => $sessions->sum('active_duration_minutes') / 60,
            'total_tasks' => $tasks->count(),
            'completed_tasks' => $tasks->where('is_converted', true)->count(),
            'pending_tasks' => $tasks->where('is_converted', false)->count(),
            'overdue_tasks' => $tasks->where('created_at', '<', now()->subDays(7))->where('is_converted', false)->count(),
        ];
    }

    /**
     * Get statistics for a specific telecaller
     */
    private function getTelecallerStats($userId, $startDate, $endDate)
    {
        $sessions = TelecallerSession::where('user_id', $userId)
            ->whereBetween('login_time', [$startDate, $endDate]);
        
        $idleTimes = TelecallerIdleTime::where('user_id', $userId)
            ->whereBetween('idle_start_time', [$startDate, $endDate]);
        
        $tasks = Lead::where('telecaller_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate]);

        $totalSessions = $sessions->count();
        $totalLoginHours = $sessions->sum('total_duration_minutes') / 60;
        $totalIdleHours = $idleTimes->sum('idle_duration_seconds') / 3600;
        $totalActiveHours = $sessions->sum('active_duration_minutes') / 60;
        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('is_converted', true)->count();
        $pendingTasks = $tasks->where('is_converted', false)->count();
        $overdueTasks = $tasks->where('created_at', '<', now()->subDays(7))->where('is_converted', false)->count();

        return [
            'total_sessions' => $totalSessions,
            'total_login_hours' => round($totalLoginHours, 2),
            'total_idle_hours' => round($totalIdleHours, 2),
            'total_active_hours' => round($totalActiveHours, 2),
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'pending_tasks' => $pendingTasks,
            'overdue_tasks' => $overdueTasks,
            'productivity_score' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0,
            'efficiency_score' => $totalLoginHours > 0 ? round(($totalActiveHours / $totalLoginHours) * 100, 2) : 0,
        ];
    }

    /**
     * Get real-time data for dashboard
     */
    public function getRealtimeData()
    {
        $activeSessions = TelecallerSession::active()
            ->with('user')
            ->get();

        $recentActivities = TelecallerActivityLog::with('user')
            ->recent(30) // Last 30 minutes
            ->orderBy('activity_time', 'desc')
            ->get();

        $overdueTasks = Lead::with('telecaller')
            ->whereNotNull('telecaller_id')
            ->where('is_converted', false)
            ->where('created_at', '<', now()->subDays(7))
            ->get();

        return response()->json([
            'active_sessions' => $activeSessions,
            'recent_activities' => $recentActivities,
            'overdue_tasks' => $overdueTasks,
            'timestamp' => now()->toISOString()
        ]);
    }
}
