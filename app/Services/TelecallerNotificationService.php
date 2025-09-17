<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Lead;
use App\Models\TelecallerSession;
use App\Models\TelecallerIdleTime;
use Carbon\Carbon;

class TelecallerNotificationService
{
    /**
     * Send notification for overdue leads (tasks)
     */
    public static function notifyOverdueTasks()
    {
        $overdueTasks = Lead::whereNotNull('telecaller_id')
            ->where('is_converted', false)
            ->where('created_at', '<', now()->subDays(7))
            ->with('telecaller')
            ->get();
        
        foreach ($overdueTasks as $lead) {
            // Notify super admins about overdue leads
            $superAdmins = User::where('role_id', 1)->get();
            
            foreach ($superAdmins as $admin) {
                $daysOverdue = $lead->created_at->diffInDays(now());
                Notification::create([
                    'title' => 'Overdue Lead Alert',
                    'message' => "Lead '{$lead->title}' assigned to {$lead->telecaller->name} is overdue by {$daysOverdue} days.",
                    'type' => 'overdue_task',
                    'role_id' => 1, // Super Admin
                    'user_id' => $admin->id,
                    'data' => [
                        'lead_id' => $lead->id,
                        'telecaller_id' => $lead->telecaller_id,
                        'created_date' => $lead->created_at->toISOString(),
                        'days_overdue' => $daysOverdue
                    ]
                ]);
            }
        }
    }

    /**
     * Send notification for excessive idle time
     */
    public static function notifyExcessiveIdleTime($userId, $idleMinutes, $thresholdMinutes = 60)
    {
        if ($idleMinutes >= $thresholdMinutes) {
            $user = User::find($userId);
            $superAdmins = User::where('role_id', 1)->get();
            
            foreach ($superAdmins as $admin) {
                Notification::create([
                    'title' => 'Excessive Idle Time Alert',
                    'message' => "{$user->name} has been idle for " . round($idleMinutes) . " minutes, exceeding the threshold of {$thresholdMinutes} minutes.",
                    'type' => 'excessive_idle',
                    'role_id' => 1, // Super Admin
                    'user_id' => $admin->id,
                    'data' => [
                        'telecaller_id' => $userId,
                        'idle_minutes' => $idleMinutes,
                        'threshold_minutes' => $thresholdMinutes
                    ]
                ]);
            }
        }
    }

    /**
     * Send notification for leads due today (leads created today)
     */
    public static function notifyTasksDueToday()
    {
        $dueTodayTasks = Lead::whereNotNull('telecaller_id')
            ->where('is_converted', false)
            ->whereDate('created_at', today())
            ->with('telecaller')
            ->get();
        
        foreach ($dueTodayTasks as $lead) {
            // Notify the telecaller
            Notification::create([
                'title' => 'New Lead Assigned',
                'message' => "New lead '{$lead->title}' has been assigned to you today.",
                'type' => 'task_due_today',
                'role_id' => 3, // Telecaller
                'user_id' => $lead->telecaller_id,
                'data' => [
                    'lead_id' => $lead->id,
                    'created_date' => $lead->created_at->toISOString()
                ]
            ]);

            // Notify super admins
            $superAdmins = User::where('role_id', 1)->get();
            foreach ($superAdmins as $admin) {
                Notification::create([
                    'title' => 'New Lead Assignment',
                    'message' => "Lead '{$lead->title}' has been assigned to {$lead->telecaller->name} today.",
                    'type' => 'task_due_today_admin',
                    'role_id' => 1, // Super Admin
                    'user_id' => $admin->id,
                    'data' => [
                        'lead_id' => $lead->id,
                        'telecaller_id' => $lead->telecaller_id,
                        'created_date' => $lead->created_at->toISOString()
                    ]
                ]);
            }
        }
    }

    /**
     * Send notification for auto-logout
     */
    public static function notifyAutoLogout($userId, $sessionId)
    {
        $user = User::find($userId);
        $superAdmins = User::where('role_id', 1)->get();
        
        foreach ($superAdmins as $admin) {
            Notification::create([
                'title' => 'Auto-Logout Alert',
                'message' => "{$user->name} was automatically logged out due to inactivity (1 hour).",
                'type' => 'auto_logout',
                'role_id' => 1, // Super Admin
                'user_id' => $admin->id,
                'data' => [
                    'telecaller_id' => $userId,
                    'session_id' => $sessionId,
                    'logout_time' => now()->toISOString()
                ]
            ]);
        }
    }

    /**
     * Send notification for low productivity
     */
    public static function notifyLowProductivity($userId, $productivityScore, $threshold = 50)
    {
        if ($productivityScore < $threshold) {
            $user = User::find($userId);
            $superAdmins = User::where('role_id', 1)->get();
            
            foreach ($superAdmins as $admin) {
                Notification::create([
                    'title' => 'Low Productivity Alert',
                    'message' => "{$user->name} has a productivity score of {$productivityScore}%, which is below the threshold of {$threshold}%.",
                    'type' => 'low_productivity',
                    'role_id' => 1, // Super Admin
                    'user_id' => $admin->id,
                    'data' => [
                        'telecaller_id' => $userId,
                        'productivity_score' => $productivityScore,
                        'threshold' => $threshold
                    ]
                ]);
            }
        }
    }

    /**
     * Send notification for long sessions
     */
    public static function notifyLongSession($userId, $sessionHours, $thresholdHours = 8)
    {
        if ($sessionHours >= $thresholdHours) {
            $user = User::find($userId);
            $superAdmins = User::where('role_id', 1)->get();
            
            foreach ($superAdmins as $admin) {
                Notification::create([
                    'title' => 'Long Session Alert',
                    'message' => "{$user->name} has been logged in for {$sessionHours} hours, which exceeds the recommended threshold of {$thresholdHours} hours.",
                    'type' => 'long_session',
                    'role_id' => 1, // Super Admin
                    'user_id' => $admin->id,
                    'data' => [
                        'telecaller_id' => $userId,
                        'session_hours' => $sessionHours,
                        'threshold_hours' => $thresholdHours
                    ]
                ]);
            }
        }
    }

    /**
     * Send daily summary notifications
     */
    public static function sendDailySummary()
    {
        $today = Carbon::today();
        $superAdmins = User::where('role_id', 1)->get();
        
        // Get today's statistics
        $totalSessions = TelecallerSession::whereDate('login_time', $today)->count();
        $totalTasks = Lead::whereDate('created_at', $today)->whereNotNull('telecaller_id')->count();
        $completedTasks = Lead::whereDate('created_at', $today)->whereNotNull('telecaller_id')->where('is_converted', true)->count();
        $overdueTasks = Lead::whereNotNull('telecaller_id')->where('is_converted', false)->where('created_at', '<', now()->subDays(7))->count();
        
        foreach ($superAdmins as $admin) {
            Notification::create([
                'title' => 'Daily Summary Report',
                'message' => "Today's Summary: {$totalSessions} sessions, {$totalTasks} tasks created, {$completedTasks} completed, {$overdueTasks} overdue.",
                'type' => 'daily_summary',
                'role_id' => 1, // Super Admin
                'user_id' => $admin->id,
                'data' => [
                    'date' => $today->toISOString(),
                    'total_sessions' => $totalSessions,
                    'total_tasks' => $totalTasks,
                    'completed_tasks' => $completedTasks,
                    'overdue_tasks' => $overdueTasks
                ]
            ]);
        }
    }

    /**
     * Check and send all alerts
     */
    public static function checkAndSendAlerts()
    {
        // Check overdue tasks
        self::notifyOverdueTasks();
        
        // Check tasks due today
        self::notifyTasksDueToday();
        
        // Check for excessive idle time
        $activeSessions = TelecallerSession::active()->get();
        foreach ($activeSessions as $session) {
            $idleMinutes = $session->idleTimes()->where('is_active', true)->sum('idle_duration_seconds') / 60;
            if ($idleMinutes > 0) {
                self::notifyExcessiveIdleTime($session->user_id, $idleMinutes);
            }
        }
        
        // Check for long sessions
        foreach ($activeSessions as $session) {
            $sessionHours = $session->calculateTotalDuration() / 60;
            self::notifyLongSession($session->user_id, $sessionHours);
        }
    }
}
