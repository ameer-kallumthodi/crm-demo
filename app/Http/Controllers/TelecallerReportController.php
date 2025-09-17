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
use Illuminate\Support\Facades\Log;
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
            ->whereBetween('login_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($telecallerId) {
            $query->where('user_id', $telecallerId);
        }

        $sessions = $query->orderBy('login_time', 'desc')->get();


        // If no sessions found in date range, get all sessions as fallback
        if ($sessions->isEmpty()) {
            $sessions = TelecallerSession::with(['user', 'idleTimes', 'activityLogs'])
                ->orderBy('login_time', 'desc')
                ->get();
        }

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
        $startDate = $request->get('start_date', Carbon::now()->subDays(90)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        

        $telecaller = User::findOrFail($userId);
        
        $sessions = TelecallerSession::where('user_id', $userId)
            ->whereBetween('login_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with(['idleTimes', 'activityLogs'])
            ->orderBy('login_time', 'desc')
            ->get();
            
        // If no sessions found in the date range, get all sessions for this user
        if ($sessions->isEmpty()) {
            $sessions = TelecallerSession::where('user_id', $userId)
                ->with(['idleTimes', 'activityLogs'])
                ->orderBy('login_time', 'desc')
                ->get();
        }

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
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.telecaller-tracking.pdf-report', [
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
        $sessions = TelecallerSession::whereBetween('login_time', [$startDate, $endDate])
            ->with('idleTimes')
            ->get();
        $tasks = Lead::whereBetween('created_at', [$startDate, $endDate])->whereNotNull('telecaller_id');

        // Calculate total login hours
        $totalLoginMinutes = 0;
        foreach ($sessions as $session) {
            if ($session->total_duration_minutes && $session->total_duration_minutes > 0) {
                $totalLoginMinutes += $session->total_duration_minutes;
            } else {
                $calculatedDuration = $session->calculateTotalDuration();
                if ($calculatedDuration > 0) {
                    $totalLoginMinutes += $calculatedDuration / 60; // Convert seconds to minutes
                }
            }
        }
        $totalLoginHours = $totalLoginMinutes / 60;

        // Calculate total idle hours from idle_times table directly
        $totalIdleSeconds = TelecallerIdleTime::whereBetween('idle_start_time', [$startDate, $endDate])
            ->sum('idle_duration_seconds');
        
        $totalIdleMinutes = $totalIdleSeconds / 60;
        $totalIdleHours = $totalIdleSeconds / 3600;

        // Calculate total active hours
        $totalActiveMinutes = 0;
        foreach ($sessions as $session) {
            if ($session->active_duration_minutes && $session->active_duration_minutes > 0) {
                $totalActiveMinutes += $session->active_duration_minutes;
            } else {
                $calculatedActiveDuration = $session->calculateActiveDuration();
                if ($calculatedActiveDuration > 0) {
                    $totalActiveMinutes += $calculatedActiveDuration / 60; // Convert seconds to minutes
                }
            }
        }
        $totalActiveHours = $totalActiveMinutes / 60;

        return [
            'total_sessions' => $sessions->count(),
            'total_login_hours' => round($totalLoginHours, 2),
            'total_idle_hours' => round($totalIdleHours, 2),
            'total_idle_seconds' => $totalIdleSeconds, // Add seconds for direct conversion
            'total_active_hours' => round($totalActiveHours, 2),
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
            ->whereBetween('login_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with('idleTimes')
            ->get();
            
        // If no sessions found in date range, get all sessions for this user (fallback)
        if ($sessions->isEmpty()) {
            $sessions = TelecallerSession::where('user_id', $userId)
                ->with('idleTimes')
                ->get();
        }
        
        $idleTimes = TelecallerIdleTime::where('user_id', $userId)
            ->whereBetween('idle_start_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get();
            Log::info('Idle times:', ['idle_times' => $idleTimes]);
            
        // If no idle times found in date range, get all idle times for this user (fallback)
        if ($idleTimes->isEmpty()) {
            $idleTimes = TelecallerIdleTime::where('user_id', $userId)->get();
        }
        
        $tasks = Lead::where('telecaller_id', $userId)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get();

        $totalSessions = $sessions->count();
        
        // Calculate total login hours (sum of all session durations)
        $totalLoginMinutes = 0;
        foreach ($sessions as $session) {
            if ($session->total_duration_minutes && $session->total_duration_minutes > 0) {
                $totalLoginMinutes += $session->total_duration_minutes;
            } else {
                $calculatedDuration = $session->calculateTotalDuration();
                if ($calculatedDuration > 0) {
                    $totalLoginMinutes += $calculatedDuration / 60; // Convert seconds to minutes
                }
            }
        }
        $totalLoginHours = $totalLoginMinutes / 60;
        
        // Calculate total idle hours from idle_times table directly for this user
        $totalIdleSeconds = $idleTimes->sum('idle_duration_seconds');
        
        $totalIdleMinutes = $totalIdleSeconds / 60;
        $totalIdleHours = $totalIdleSeconds / 3600;
        
        // Calculate total active hours (login time - idle time)
        $totalActiveMinutes = 0;
        foreach ($sessions as $session) {
            if ($session->active_duration_minutes && $session->active_duration_minutes > 0) {
                $totalActiveMinutes += $session->active_duration_minutes;
            } else {
                $calculatedActiveDuration = $session->calculateActiveDuration();
                if ($calculatedActiveDuration > 0) {
                    $totalActiveMinutes += $calculatedActiveDuration / 60; // Convert seconds to minutes
                }
            }
        }
        $totalActiveHours = $totalActiveMinutes / 60;
        
        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('is_converted', true)->count();
        $pendingTasks = $tasks->where('is_converted', false)->count();
        $overdueTasks = $tasks->where('created_at', '<', now()->subDays(7))->where('is_converted', false)->count();

        return [
            'total_sessions' => $totalSessions,
            'total_login_hours' => round($totalLoginHours, 2),
            'total_idle_hours' => round($totalIdleHours, 2),
            'total_idle_seconds' => $totalIdleSeconds, // Add seconds for direct conversion
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
     * Show session details page
     */
    public function sessionDetails($sessionId)
    {
        $session = TelecallerSession::with(['user', 'idleTimes', 'activityLogs'])
            ->findOrFail($sessionId);

        $activityLogs = $session->activityLogs()->orderBy('activity_time', 'desc')->get();
        $idleTimes = $session->idleTimes()->orderBy('idle_start_time', 'desc')->get();

        // Calculate session statistics
        $sessionStats = $this->calculateSessionStats($session);

        return view('admin.telecaller-tracking.session-details', compact(
            'session',
            'activityLogs',
            'idleTimes',
            'sessionStats'
        ));
    }

    /**
     * Calculate statistics for a specific session
     */
    private function calculateSessionStats($session)
    {
        // Calculate total duration
        $totalDurationMinutes = 0;
        if ($session->total_duration_minutes && $session->total_duration_minutes > 0) {
            $totalDurationMinutes = $session->total_duration_minutes;
        } else {
            $calculatedDuration = $session->calculateTotalDuration();
            if ($calculatedDuration > 0) {
                $totalDurationMinutes = $calculatedDuration / 60; // Convert seconds to minutes
            }
        }

        // Calculate active duration
        $activeDurationMinutes = 0;
        if ($session->active_duration_minutes && $session->active_duration_minutes > 0) {
            $activeDurationMinutes = $session->active_duration_minutes;
        } else {
            $calculatedActiveDuration = $session->calculateActiveDuration();
            if ($calculatedActiveDuration > 0) {
                $activeDurationMinutes = $calculatedActiveDuration / 60; // Convert seconds to minutes
            }
        }

        // Calculate idle duration from idle times table
        $idleDurationSeconds = $session->idleTimes()->sum('idle_duration_seconds');
        $idleDurationMinutes = $idleDurationSeconds / 60; // Convert seconds to minutes
        $idleDurationHours = $idleDurationSeconds / 3600; // Convert seconds to hours

        return [
            'total_duration_minutes' => round($totalDurationMinutes, 2),
            'active_duration_minutes' => round($activeDurationMinutes, 2),
            'idle_duration_minutes' => round($idleDurationMinutes, 2),
            'total_duration_hours' => round($totalDurationMinutes / 60, 2),
            'active_duration_hours' => round($activeDurationMinutes / 60, 2),
            'idle_duration_hours' => round($idleDurationHours, 2),
        ];
    }

    /**
     * Get session details via AJAX
     */
    public function getSessionDetails($sessionId)
    {
        $session = TelecallerSession::with(['idleTimes', 'activityLogs'])
            ->findOrFail($sessionId);

        $activityLogs = $session->activityLogs()->orderBy('activity_time', 'desc')->get();
        $idleTimes = $session->idleTimes()->orderBy('idle_start_time', 'desc')->get();

        return response()->json([
            'session' => $session,
            'activity_logs' => $activityLogs,
            'idle_times' => $idleTimes
        ]);
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
