<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class TelecallerTask extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'task_type',
        'priority',
        'due_date',
        'completed_at',
        'status',
        'notes',
        'estimated_duration_minutes',
        'actual_duration_minutes',
        'assigned_by'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the task (lead).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'telecaller_id');
    }

    /**
     * Get the user who assigned the task.
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Check if task is overdue.
     */
    public function isOverdue()
    {
        return $this->status !== 'completed' && $this->due_date < now();
    }

    /**
     * Check if task is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Mark task as completed.
     */
    public function markAsCompleted($notes = null, $actualDuration = null)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'notes' => $notes ? $this->notes . "\n" . $notes : $this->notes,
            'actual_duration_minutes' => $actualDuration ?? $this->actual_duration_minutes,
        ]);
    }

    /**
     * Get days until due.
     */
    public function getDaysUntilDue()
    {
        return now()->diffInDays($this->due_date, false);
    }

    /**
     * Scope for tasks by user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for tasks by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for overdue tasks.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('status', '!=', 'completed');
    }

    /**
     * Scope for tasks due today.
     */
    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', today())
                    ->where('status', '!=', 'completed');
    }

    /**
     * Scope for tasks by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for tasks by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('task_type', $type);
    }

    /**
     * Scope for tasks within date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('due_date', [$startDate, $endDate]);
    }
}
