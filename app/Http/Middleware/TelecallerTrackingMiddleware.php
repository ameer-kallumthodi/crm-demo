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
        // Only track telecallers (role_id = 3)
        if (AuthHelper::isLoggedIn() && AuthHelper::getRoleId() == 3) {
            $this->trackSession($request);
            $this->checkForAutoLogout($request);
            $this->logActivity($request);
        }

        return $next($request);
    }

    /**
     * Track user session
     */
    private function trackSession(Request $request)
    {
        $userId = AuthHelper::getUserId();
        $sessionId = session()->getId();
        
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
                // End the existing session
                $existingActiveSession->endSession('session_change');
            }

            // Use updateOrCreate to avoid duplicate key errors
            TelecallerSession::updateOrCreate(
                [
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                ],
                [
                    'login_time' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * Check for auto-logout due to inactivity
     */
    private function checkForAutoLogout(Request $request)
    {
        $userId = AuthHelper::getUserId();
        $sessionId = session()->getId();
        
        // Get the last activity for this user
        $lastActivity = TelecallerActivityLog::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->where('activity_type', '!=', 'idle_start')
            ->orderBy('activity_time', 'desc')
            ->first();

        if ($lastActivity) {
            $inactiveMinutes = $lastActivity->activity_time->diffInMinutes(now());
            
            // Auto-logout after 30 seconds of inactivity
            if ($inactiveMinutes >= 0.5) { // 0.5 minutes = 30 seconds
                $this->performAutoLogout($userId, $sessionId);
            }
        }
    }

    /**
     * Perform auto-logout
     */
    private function performAutoLogout($userId, $sessionId)
    {
        // End the current session
        $session = TelecallerSession::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->where('is_active', true)
            ->first();

        if ($session) {
            $session->endSession('auto');
        }

        // Log the auto-logout activity
        TelecallerActivityLog::create([
            'user_id' => $userId,
            'session_id' => $session ? $session->id : null,
            'activity_type' => 'logout',
            'activity_name' => 'auto_logout',
            'description' => 'Auto-logout due to inactivity (30 seconds)',
            'activity_time' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

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
        
        // Get the current session
        $session = TelecallerSession::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->where('is_active', true)
            ->first();

        if ($session) {
            // Log page view activity
            TelecallerActivityLog::create([
                'user_id' => $userId,
                'session_id' => $session->id,
                'activity_type' => 'page_view',
                'activity_name' => $request->route() ? $request->route()->getName() : 'unknown',
                'description' => 'Page view: ' . $request->path(),
                'page_url' => $request->fullUrl(),
                'activity_time' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }
    }
}
