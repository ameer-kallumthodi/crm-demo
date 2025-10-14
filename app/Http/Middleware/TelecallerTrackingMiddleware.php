<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\AuthHelper;
use App\Models\TelecallerSession;
use App\Models\TelecallerActivityLog;
// use App\Services\TelecallerNotificationService; // Removed as per requirement
use Carbon\Carbon;

class TelecallerTrackingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only track telecallers (role_id = 3) and ensure user is properly authenticated
        if (AuthHelper::isLoggedIn() && AuthHelper::getRoleId() == 3 && AuthHelper::getUserId() > 0) {
            $this->cleanupOrphanedSessions();
            $this->checkWorkingHours($request);
            $this->trackSession($request);
            $this->checkForAutoLogout($request);
            $this->logActivity($request);
        }

        return $next($request);
    }

    /**
     * Check if current time is within working hours (9:30 AM - 7:30 PM)
     * If outside working hours, auto-logout the user
     */
    private function checkWorkingHours(Request $request)
    {
        $currentTime = now();
        $currentHour = $currentTime->hour;
        $currentMinute = $currentTime->minute;
        $currentTimeMinutes = ($currentHour * 60) + $currentMinute;
        
        // Working hours: 9:30 AM (570 minutes) to 7:30 PM (1140 minutes)
        $workStartMinutes = (9 * 60) + 30; // 9:30 AM = 570 minutes
        $workEndMinutes = (19 * 60) + 30;  // 7:30 PM = 1140 minutes
        
        // Check if current time is outside working hours
        if ($currentTimeMinutes < $workStartMinutes || $currentTimeMinutes > $workEndMinutes) {
            $this->performWorkingHoursLogout($request);
        }
    }

    /**
     * Perform logout due to non-working hours
     */
    private function performWorkingHoursLogout(Request $request)
    {
        $userId = AuthHelper::getUserId();
        $sessionId = session()->getId();
        
        // Ensure user is properly authenticated before proceeding
        if (!$userId || $userId <= 0) {
            return;
        }
        
        // Get the current active session
        $session = TelecallerSession::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->where('is_active', true)
            ->first();

        if ($session) {
            // End the session with working hours logout type
            $session->endSession('working_hours');
        }

        // Destroy the session completely
        session()->invalidate();
        session()->regenerateToken();
        
        // Redirect to root URL with working hours message
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'You are outside working hours (9:30 AM - 7:30 PM)',
                'redirect' => url('/')
            ], 403);
        } else {
            return redirect(url('/'))->with('error', 'You are outside working hours (9:30 AM - 7:30 PM)');
        }
    }

    /**
     * Cleanup orphaned sessions (sessions from previous days that are still active)
     */
    private function cleanupOrphanedSessions()
    {
        $userId = AuthHelper::getUserId();
        
        // Ensure user is properly authenticated before proceeding
        if (!$userId || $userId <= 0) {
            return;
        }
        
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');
        
        // Find all active sessions for this user from previous days
        $orphanedSessions = TelecallerSession::where('user_id', $userId)
            ->where('is_active', true)
            ->whereRaw('DATE(login_time) < ?', [$today])
            ->get();

        foreach ($orphanedSessions as $session) {
            // End the orphaned session with system logout type
            $session->endSession('system');
        }

        // Also cleanup sessions that are more than 24 hours old (extra safety)
        $veryOldSessions = TelecallerSession::where('user_id', $userId)
            ->where('is_active', true)
            ->where('login_time', '<', now()->subHours(24))
            ->get();

        foreach ($veryOldSessions as $session) {
            // End very old sessions
            $session->endSession('system');
        }
    }

    /**
     * Track user session
     */
    private function trackSession(Request $request)
    {
        $userId = AuthHelper::getUserId();
        $sessionId = session()->getId();
        
        // Ensure user is properly authenticated before proceeding
        if (!$userId || $userId <= 0) {
            // User is not authenticated, skip session tracking
            return;
        }
        
        // Check if there's an active session for this user with this session ID
        $activeSession = TelecallerSession::where('user_id', $userId)
            ->where('is_active', true)
            ->where('session_id', $sessionId)
            ->first();

        if (!$activeSession) {
            // Check if there's any active session for this user (different session ID)
            $existingActiveSession = TelecallerSession::where('user_id', $userId)
                ->where('is_active', true)
                ->first();

            if ($existingActiveSession) {
                // Check if the existing session is from a different day
                $sessionLoginDate = $existingActiveSession->login_time->format('Y-m-d');
                $todayDate = now()->format('Y-m-d');
                
                if ($sessionLoginDate !== $todayDate) {
                    // Session is from a different day - end it and create new session
                    $existingActiveSession->endSession('session_change');
                } else {
                    // Session is from today but different session ID - end it
                    $existingActiveSession->endSession('session_change');
                }
            }

            // Check if session already exists for this user and session ID
            $existingSession = TelecallerSession::where('user_id', $userId)
                ->where('session_id', $sessionId)
                ->first();

            if (!$existingSession) {
                // Create new session only if it doesn't exist
                TelecallerSession::create([
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'login_time' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'is_active' => true,
                ]);
            } else {
                // Reactivate existing session without updating login_time
                $existingSession->update([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'is_active' => true,
                    'logout_time' => null, // Clear logout time if session was ended
                ]);
            }
        }
    }

    /**
     * Check for auto-logout due to inactivity
     * Note: This middleware check is disabled as auto-logout is handled by JavaScript
     * The JavaScript handles: 20 minutes idle + 20 seconds logout threshold
     */
    private function checkForAutoLogout(Request $request)
    {
        // Auto-logout is now handled by JavaScript telecaller-tracking.js
        // JavaScript handles: 20 minutes idle detection + 20 seconds logout countdown
        // This middleware check is disabled to avoid conflicts
        return;
    }

    /**
     * Perform auto-logout
     */
    private function performAutoLogout($userId, $sessionId)
    {
        // Ensure user is properly authenticated before proceeding
        if (!$userId || $userId <= 0) {
            return;
        }
        
        // End the current session
        $session = TelecallerSession::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->where('is_active', true)
            ->first();

        if ($session) {
            $session->endSession('auto');
        }

        // Auto-logout activity (not logged as it's internal tracking)

        // Note: Notification creation removed as per requirement
        // TelecallerNotificationService::notifyAutoLogout($userId, $sessionId);

        // Destroy the session completely
        session()->invalidate();
        session()->regenerateToken();
    }

    /**
     * Log user activity
     */
    private function logActivity(Request $request)
    {
        $userId = AuthHelper::getUserId();
        $sessionId = session()->getId();
        
        // Ensure user is properly authenticated before proceeding
        if (!$userId || $userId <= 0) {
            return;
        }
        
        // Get the current session
        $session = TelecallerSession::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->where('is_active', true)
            ->first();

        // Page view activity (not logged as it's handled by JavaScript)
    }
}
