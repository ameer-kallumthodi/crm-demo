<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConvertedStudentSupportDetail extends Model
{
    protected $fillable = [
        'converted_student_id',
        'registration_status',
        'technology_side',
        'student_status',
        'call_1',
        'app',
        'whatsapp_group',
        'telegram_group',
        'problems',
        'support_notes',
        'support_status',
        'last_support_contact',
        'support_priority',
        'last_feedback',
    ];

    protected $casts = [
        'last_support_contact' => 'datetime',
        'last_feedback' => 'datetime',
    ];

    /**
     * Get the converted lead that owns the support details.
     */
    public function convertedLead(): BelongsTo
    {
        return $this->belongsTo(ConvertedLead::class, 'converted_student_id');
    }
}
