<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class TelecallerIdleTime extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'session_id',
        'user_id',
        'idle_start_time',
        'idle_end_time',
        'idle_duration_seconds',
        'idle_type',
        'is_active'
    ];

    protected $casts = [
        'idle_start_time' => 'datetime',
        'idle_end_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the session that owns the idle time.
     */
    public function session()
    {
        return $this->belongsTo(TelecallerSession::class, 'session_id');
    }

    /**
     * Get the user that owns the idle time.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate idle duration in seconds.
     */
    public function calculateDuration()
    {
        if ($this->idle_end_time) {
            return $this->idle_start_time->diffInSeconds($this->idle_end_time);
        }
        return $this->idle_start_time->diffInSeconds(now());
    }

    /**
     * End the idle time period.
     */
    public function endIdleTime()
    {
        $this->update([
            'idle_end_time' => now(),
            'idle_duration_seconds' => $this->calculateDuration(),
            'is_active' => false,
        ]);
    }

    /**
     * Check if idle time is currently active.
     */
    public function isActive()
    {
        return $this->is_active && !$this->idle_end_time;
    }

    /**
     * Scope for active idle times.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for idle times by user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for idle times within date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('idle_start_time', [$startDate, $endDate]);
    }

    /**
     * Scope for idle times by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('idle_type', $type);
    }
}
