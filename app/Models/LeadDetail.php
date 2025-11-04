<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'leads_details';

    protected $fillable = [
        'lead_id',
        'course_id',
        'university_id',
        'university_course_id',
        'course_type',
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
        'sub_course_id',
        'class',
        'second_language',
        'passed_year',
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
        // Document verification fields
        'sslc_verification_status',
        'sslc_verified_by',
        'sslc_verified_at',
        'plustwo_verification_status',
        'plustwo_verified_by',
        'plustwo_verified_at',
        'ug_verification_status',
        'ug_verified_by',
        'ug_verified_at',
        'passport_photo_verification_status',
        'passport_photo_verified_by',
        'passport_photo_verified_at',
        'adhar_front_verification_status',
        'adhar_front_verified_by',
        'adhar_front_verified_at',
        'adhar_back_verification_status',
        'adhar_back_verified_by',
        'adhar_back_verified_at',
        'signature_verification_status',
        'signature_verified_by',
        'signature_verified_at',
        'birth_certificate_verification_status',
        'birth_certificate_verified_by',
        'birth_certificate_verified_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'reviewed_at' => 'datetime',
        'sslc_verified_at' => 'datetime',
        'plustwo_verified_at' => 'datetime',
        'ug_verified_at' => 'datetime',
        'passport_photo_verified_at' => 'datetime',
        'adhar_front_verified_at' => 'datetime',
        'adhar_back_verified_at' => 'datetime',
        'signature_verified_at' => 'datetime',
        'birth_certificate_verified_at' => 'datetime',
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

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function universityCourse()
    {
        return $this->belongsTo(UniversityCourse::class);
    }

    public function subCourse()
    {
        return $this->belongsTo(SubCourse::class, 'sub_course_id');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Document verification relationships
    public function sslcVerifiedBy()
    {
        return $this->belongsTo(User::class, 'sslc_verified_by');
    }

    public function plustwoVerifiedBy()
    {
        return $this->belongsTo(User::class, 'plustwo_verified_by');
    }

    public function ugVerifiedBy()
    {
        return $this->belongsTo(User::class, 'ug_verified_by');
    }

    public function passportPhotoVerifiedBy()
    {
        return $this->belongsTo(User::class, 'passport_photo_verified_by');
    }

    public function adharFrontVerifiedBy()
    {
        return $this->belongsTo(User::class, 'adhar_front_verified_by');
    }

    public function adharBackVerifiedBy()
    {
        return $this->belongsTo(User::class, 'adhar_back_verified_by');
    }

    public function signatureVerifiedBy()
    {
        return $this->belongsTo(User::class, 'signature_verified_by');
    }

    public function birthCertificateVerifiedBy()
    {
        return $this->belongsTo(User::class, 'birth_certificate_verified_by');
    }

    /**
     * Get the SSLC certificates for this lead detail.
     */
    public function sslcCertificates()
    {
        return $this->hasMany(SSLCertificate::class);
    }
}