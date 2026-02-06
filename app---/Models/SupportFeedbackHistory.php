<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportFeedbackHistory extends Model
{
    protected $table = 'support_feedback_history';

    protected $fillable = [
        'converted_student_id',
        'created_by',
        'feedback_type',
        'feedback_content',
        'feedback_status',
        'priority',
        'follow_up_date',
        'notes',
    ];

    protected $casts = [
        'follow_up_date' => 'datetime',
    ];

    /**
     * Get the converted lead that owns the feedback.
     */
    public function convertedLead(): BelongsTo
    {
        return $this->belongsTo(ConvertedLead::class, 'converted_student_id');
    }

    /**
     * Get the user who created the feedback.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get feedback type options.
     */
    public static function getFeedbackTypes(): array
    {
        return [
            'general' => 'General Feedback',
            'call' => 'Call Feedback',
            'issue' => 'Issue Report',
            'resolution' => 'Resolution Update',
            'follow_up' => 'Follow Up',
            'complaint' => 'Complaint',
            'suggestion' => 'Suggestion',
        ];
    }

    /**
     * Get feedback status options.
     */
    public static function getFeedbackStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            'escalated' => 'Escalated',
        ];
    }

    /**
     * Get priority options.
     */
    public static function getPriorities(): array
    {
        return [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];
    }
}
