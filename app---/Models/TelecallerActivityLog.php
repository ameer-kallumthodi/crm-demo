<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TelecallerActivityLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'session_id',
        'activity_type',
        'activity_name',
        'description',
        'page_url',
        'ip_address',
        'user_agent',
        'metadata',
        'activity_time'
    ];

    protected $casts = [
        'activity_time' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the activity log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the session that owns the activity log.
     */
    public function session()
    {
        return $this->belongsTo(TelecallerSession::class, 'session_id');
    }

    /**
     * Scope for activities by user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for activities by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Scope for activities within date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('activity_time', [$startDate, $endDate]);
    }

    /**
     * Scope for activities by session.
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope for recent activities.
     */
    public function scopeRecent($query, $minutes = 60)
    {
        return $query->where('activity_time', '>=', now()->subMinutes($minutes));
    }
}
