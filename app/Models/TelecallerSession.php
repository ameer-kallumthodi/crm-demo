<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class TelecallerSession extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'session_id',
        'login_time',
        'logout_time',
        'logout_type',
        'total_duration_minutes',
        'active_duration_minutes',
        'idle_duration_minutes',
        'ip_address',
        'user_agent',
        'is_active'
    ];

    protected $casts = [
        'login_time' => 'datetime',
        'logout_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the session.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the idle times for the session.
     */
    public function idleTimes()
    {
        return $this->hasMany(TelecallerIdleTime::class, 'session_id');
    }

    /**
     * Get the activity logs for the session.
     */
    public function activityLogs()
    {
        return $this->hasMany(TelecallerActivityLog::class, 'session_id');
    }

    /**
     * Calculate total session duration in minutes.
     */
    public function calculateTotalDuration()
    {
        if ($this->logout_time) {
            return $this->login_time->diffInMinutes($this->logout_time);
        }
        return $this->login_time->diffInMinutes(now());
    }

    /**
     * Calculate active duration (excluding idle time).
     */
    public function calculateActiveDuration()
    {
        $totalDuration = $this->calculateTotalDuration();
        $idleDuration = $this->idleTimes()->sum('idle_duration_seconds') / 60; // Convert to minutes
        return max(0, $totalDuration - $idleDuration);
    }

    /**
     * Check if session is currently active.
     */
    public function isActive()
    {
        return $this->is_active && !$this->logout_time;
    }

    /**
     * Mark session as ended.
     */
    public function endSession($logoutType = 'manual')
    {
        $this->update([
            'logout_time' => now(),
            'logout_type' => $logoutType,
            'is_active' => false,
            'total_duration_minutes' => $this->calculateTotalDuration(),
            'active_duration_minutes' => $this->calculateActiveDuration(),
            'idle_duration_minutes' => $this->idleTimes()->sum('idle_duration_seconds') / 60,
        ]);
    }

    /**
     * Scope for active sessions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for sessions by user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for sessions within date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('login_time', [$startDate, $endDate]);
    }
}
