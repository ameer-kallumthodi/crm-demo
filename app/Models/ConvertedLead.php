<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConvertedLead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lead_id',
        'name',
        'code',
        'phone',
        'email',
        'register_number',
        'course_id',
        'academic_assistant_id',
        'batch_id',
        'board_id',
        'subject_id',
        'remarks',
        'created_by',
        'updated_by',
        'deleted_by',
        'reg_updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'reg_updated_at' => 'datetime',
    ];

    // Relationships
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function leadDetail()
    {
        return $this->hasOne(LeadDetail::class, 'lead_id', 'lead_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicAssistant()
    {
        return $this->belongsTo(User::class, 'academic_assistant_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function regUpdatedBy()
    {
        return $this->belongsTo(User::class, 'reg_updated_by');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'student_id');
    }

    public function studentDetails()
    {
        return $this->hasOne(ConvertedStudentDetail::class);
    }

    public function niosStudentDetails()
    {
        return $this->hasOne(ConvertedStudentDetail::class)->where('course_id', 1);
    }

    public function bosseStudentDetails()
    {
        return $this->hasOne(ConvertedStudentDetail::class)->where('course_id', 2);
    }

    public function medicalCodingStudentDetails()
    {
        return $this->hasOne(ConvertedStudentDetail::class)->where('course_id', 3);
    }

    /**
     * Override the delete method to set deleted_by
     */
    public function delete()
    {
        $this->deleted_by = \App\Helpers\AuthHelper::getCurrentUserId();
        $this->save();
        
        return parent::delete();
    }
}
