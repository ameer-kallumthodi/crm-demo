<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TelecallerIdleTime;
use App\Models\TelecallerActivityLog;
use App\Models\TelecallerSession;
use App\Helpers\AuthHelper;
use Carbon\Carbon;

class TelecallerTrackingController extends Controller
{
    /**
     * Track idle time start
     */
    public function startIdleTime(Request $request)
    {
        $userId = AuthHelper::getUserId();
        $sessionId = session()->getId();
        
        // Get the current active session
        $session = TelecallerSession::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->where('is_active', true)
            ->first();

        if (!$session) {
            return response()->json(['error' => 'No active session found'], 400);
        }

        // Check if there's already an active idle time
        $activeIdleTime = TelecallerIdleTime::where('user_id', $userId)
            ->where('session_id', $session->id)
            ->where('is_active', true)
            ->first();

        if ($activeIdleTime) {
            return response()->json(['message' => 'Idle time already active'], 200);
        }

        // Create new idle time record
        $idleTime = TelecallerIdleTime::create([
            'session_id' => $session->id,
            'user_id' => $userId,
            'idle_start_time' => now(),
            'idle_type' => $request->input('idle_type', 'general'),
            'is_active' => true,
        ]);

        // Log the activity
        TelecallerActivityLog::create([
            'user_id' => $userId,
            'session_id' => $session->id,
            'activity_type' => 'idle_start',
            'activity_name' => 'idle_time_started',
            'description' => 'User became idle',
            'activity_time' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'idle_time_id' => $idleTime->id,
            'message' => 'Idle time tracking started'
        ]);
    }

    /**
     * Track idle time end
     */
    public function endIdleTime(Request $request)
    {
        $userId = AuthHelper::getUserId();
        $sessionId = session()->getId();
        
        // Get the current active session
        $session = TelecallerSession::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->where('is_active', true)
            ->first();

        if (!$session) {
            return response()->json(['error' => 'No active session found'], 400);
        }

        // Get the active idle time
        $idleTime = TelecallerIdleTime::where('user_id', $userId)
            ->where('session_id', $session->id)
            ->where('is_active', true)
            ->first();

        if (!$idleTime) {
            return response()->json(['error' => 'No active idle time found'], 400);
        }

        // End the idle time
        $idleTime->endIdleTime();

        // Log the activity
        TelecallerActivityLog::create([
            'user_id' => $userId,
            'session_id' => $session->id,
            'activity_type' => 'idle_end',
            'activity_name' => 'idle_time_ended',
            'description' => 'User became active again',
            'activity_time' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'idle_duration' => $idleTime->idle_duration_seconds,
            'message' => 'Idle time tracking ended'
        ]);
    }

    /**
     * Sync idle time data from browser
     */
    public function syncIdleTime(Request $request)
    {
        $userId = AuthHelper::getUserId();
        $sessionId = session()->getId();
        
        // Get the current active session
        $session = TelecallerSession::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->where('is_active', true)
            ->first();

        if (!$session) {
            return response()->json(['error' => 'No active session found'], 400);
        }

        $idleData = $request->input('idle_data', []);
        $totalIdleSeconds = 0;

        foreach ($idleData as $idle) {
            $startTime = Carbon::parse($idle['start_time']);
            $endTime = Carbon::parse($idle['end_time']);
            $duration = $startTime->diffInSeconds($endTime);

            TelecallerIdleTime::create([
                'session_id' => $session->id,
                'user_id' => $userId,
                'idle_start_time' => $startTime,
                'idle_end_time' => $endTime,
                'idle_duration_seconds' => $duration,
                'idle_type' => $idle['type'] ?? 'general',
                'is_active' => false,
            ]);

            $totalIdleSeconds += $duration;
        }

        // Update session with total idle time
        $session->update([
            'idle_duration_minutes' => $totalIdleSeconds / 60,
            'active_duration_minutes' => $session->calculateActiveDuration(),
        ]);

        return response()->json([
            'success' => true,
            'total_idle_seconds' => $totalIdleSeconds,
            'message' => 'Idle time data synced successfully'
        ]);
    }

    /**
     * Log user activity
     */
    public function logActivity(Request $request)
    {
        $userId = AuthHelper::getUserId();
        $sessionId = session()->getId();
        
        // Get the current active session
        $session = TelecallerSession::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->where('is_active', true)
            ->first();

        if (!$session) {
            return response()->json(['error' => 'No active session found'], 400);
        }

        TelecallerActivityLog::create([
            'user_id' => $userId,
            'session_id' => $session->id,
            'activity_type' => $request->input('activity_type', 'action'),
            'activity_name' => $request->input('activity_name', 'unknown'),
            'description' => $request->input('description', ''),
            'page_url' => $request->input('page_url', ''),
            'activity_time' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'metadata' => $request->input('metadata', []),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Activity logged successfully'
        ]);
    }

    /**
     * Get current session data
     */
    public function getCurrentSession(Request $request)
    {
        $userId = AuthHelper::getUserId();
        $sessionId = session()->getId();
        
        $session = TelecallerSession::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->where('is_active', true)
            ->with(['idleTimes', 'activityLogs'])
            ->first();

        if (!$session) {
            return response()->json(['error' => 'No active session found'], 404);
        }

        return response()->json([
            'session' => $session,
            'total_duration' => $session->calculateTotalDuration(),
            'active_duration' => $session->calculateActiveDuration(),
            'idle_duration' => $session->idleTimes()->sum('idle_duration_seconds') / 60,
        ]);
    }

    /**
     * Handle auto-logout due to inactivity
     */
    public function autoLogout(Request $request)
    {
        try {
            $userId = AuthHelper::getUserId();
            $sessionId = session()->getId();

            if (!$userId) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            // Get the current active session
            $session = TelecallerSession::where('user_id', $userId)
                ->where('session_id', $sessionId)
                ->where('is_active', true)
                ->first();

            if ($session) {
                // End the session
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
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Note: Notification creation removed as per requirement
            // TelecallerNotificationService::notifyAutoLogout($userId, $sessionId);

            // Destroy the session completely
            session()->invalidate();
            session()->regenerateToken();

            return response()->json([
                'success' => true,
                'message' => 'Auto-logout completed successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Auto-logout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Auto-logout failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
