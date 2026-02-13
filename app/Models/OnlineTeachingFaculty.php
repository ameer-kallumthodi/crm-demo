<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnlineTeachingFaculty extends Model
{
    use SoftDeletes;

    protected $table = 'online_teaching_faculties';

    protected $fillable = [
        // Form tracking
        'form_token',
        'form_filled_at',

        // A. Personal Details
        'full_name',
        'date_of_birth',
        'gender',
        'primary_mobile_number',
        'alternate_contact_number',
        'official_email_address',
        'father_name',
        'mother_name',
        'address_house_name_flat_no',
        'address_area_locality',
        'address_city',
        'address_district',
        'address_state',
        'address_pin_code',
        'highest_educational_qualification',
        'additional_certifications',
        'teaching_experience',
        'department_name',
        'department_id',

        // B. Documents
        'document_resume_cv',
        'document_10th_certificate',
        'document_educational_qualification_certificates',
        'document_aadhaar_front',
        'document_aadhaar_back',
        'document_other_1',
        'document_other_2',

        // C. HOD Review & Hiring Tracking
        'faculty_id',
        'class_level',
        'employment_type',
        'work_schedule_mode',
        'candidate_status',
        'preferred_teaching_platform',
        'technical_readiness_confirmation',
        'demo_class_date',
        'demo_conducted_by',
        'offer_letter_issued_date',
        'joining_date',
        'remarks',
        'offer_letter_upload',
        'deleted_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'teaching_experience' => 'boolean',
        'demo_class_date' => 'date',
        'offer_letter_issued_date' => 'date',
        'joining_date' => 'date',
        'form_filled_at' => 'datetime',
    ];

    /**
     * Get the department that the faculty belongs to
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
