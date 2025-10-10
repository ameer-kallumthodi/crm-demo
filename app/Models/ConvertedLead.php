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
        'dob',
        'username',
        'password',
        'status',
        'ref_no',
        'register_number',
        'course_id',
        'university_id',
        'academic_assistant_id',
        'batch_id',
        'board_id',
        'subject_id',
        'admission_batch_id',
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

    public function university()
    {
        return $this->belongsTo(University::class);
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

    public function admissionBatch()
    {
        return $this->belongsTo(AdmissionBatch::class);
    }


    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'student_id');
    }

    public function studentDetails()
    {
        return $this->hasOne(ConvertedStudentDetail::class, 'converted_lead_id');
    }

    public function idCards()
    {
        return $this->hasMany(ConvertedLeadIdCard::class, 'converted_lead_id');
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

    /**
     * Simple encryption for password
     */
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = base64_encode($value);
        }
    }

    /**
     * Simple decryption for password
     */
    public function getPasswordAttribute($value)
    {
        if ($value) {
            return base64_decode($value);
        }
        return $value;
    }

    /**
     * Proxy methods to access fields from ConvertedStudentDetail
     */
    public function getRegFeeAttribute()
    {
        return $this->studentDetails?->reg_fee;
    }

    public function getExamFeeAttribute()
    {
        return $this->studentDetails?->exam_fee;
    }

    public function getEnrollNoAttribute()
    {
        return $this->studentDetails?->enroll_no;
    }

    public function getIdCardAttribute()
    {
        return $this->studentDetails?->id_card;
    }

    public function getTmaAttribute()
    {
        return $this->studentDetails?->tma;
    }

    /**
     * Setter methods to update fields in ConvertedStudentDetail
     */
    public function setRegFeeAttribute($value)
    {
        if ($this->studentDetails) {
            $this->studentDetails->update(['reg_fee' => $value]);
        }
    }

    public function setExamFeeAttribute($value)
    {
        if ($this->studentDetails) {
            $this->studentDetails->update(['exam_fee' => $value]);
        }
    }

    public function setEnrollNoAttribute($value)
    {
        if ($this->studentDetails) {
            $this->studentDetails->update(['enroll_no' => $value]);
        }
    }

    public function setIdCardAttribute($value)
    {
        if ($this->studentDetails) {
            $this->studentDetails->update(['id_card' => $value]);
        }
    }

    public function setTmaAttribute($value)
    {
        if ($this->studentDetails) {
            $this->studentDetails->update(['tma' => $value]);
        }
    }
}
