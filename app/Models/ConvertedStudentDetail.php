<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConvertedStudentDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lead_id',
        'course_id',
        'student_name',
        'father_name',
        'mother_name',
        'date_of_birth',
        'email',
        'personal_number',
        'personal_code',
        'parents_number',
        'parents_code',
        'whatsapp_number',
        'whatsapp_code',
        'subject_id',
        'batch_id',
        'class',
        'second_language',
        'street',
        'locality',
        'post_office',
        'district',
        'state',
        'pin_code',
        'birth_certificate',
        'passport_photo',
        'adhar_front',
        'adhar_back',
        'signature',
        'plustwo_certificate',
        'sslc_certificate',
        'message',
        'status',
        'admin_remarks',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Accessors
    public function getClassLabelAttribute()
    {
        return match($this->class) {
            'sslc' => 'SSLC',
            'plustwo' => 'Plus Two',
            default => $this->class
        };
    }

    public function getSecondLanguageLabelAttribute()
    {
        return match($this->second_language) {
            'malayalam' => 'Malayalam',
            'hindi' => 'Hindi',
            default => $this->second_language
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => $this->status
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }
}