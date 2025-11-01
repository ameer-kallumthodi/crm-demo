<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConvertedStudentMentorDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'converted_student_id',
        'application_number',
        'subject_id',
        'registration_status',
        'technology_side',
        'student_status',
        'call_1',
        'app',
        'whatsapp_group',
        'telegram_group',
        'problems',
        'call_2',
        'mentor_live_1',
        'first_live',
        'first_exam_registration',
        'first_exam',
        'call_3',
        'mentor_live_2',
        'second_live',
        'second_exam',
        'call_4',
        'mentor_live_3',
        'model_exam_live',
        'model_exam',
        'practical',
        'call_5',
        'mentor_live_4',
        'self_registration',
        'call_6',
        'assignment',
        'exam_fees',
        'pcp_class',
        'call_7',
        'practical_record',
        'mock_test',
        'call_8',
        'id_card',
        'practical_hall_ticket',
        'admit_card',
        'call_9',
        'particle_exam',
        'theory_hall_ticket',
        'mentor_live_5',
        'call_10',
        'exam_subject_1',
        'exam_subject_2',
        'exam_subject_3',
        'exam_subject_4',
        'exam_subject_5',
        'exam_subject_6',
        // E-School and Eduthanzeel specific fields
        'screening_date',
        'screening_officer',
        'class_time',
        'tutor_phone_number',
        'class_status',
        'first_pa',
        'first_pa_mark',
        'feedback_call_1',
        'first_pa_remarks',
        'second_pa',
        'second_pa_mark',
        'feedback_call_2',
        'second_pa_remarks',
        'third_pa',
        'third_pa_mark',
        'feedback_call_3',
        'third_pa_remarks',
        'certification_exam',
        'certification_exam_mark',
        'course_completion_feedback',
        'certificate_collection',
        'continuing_studies',
        'reason',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'screening_date' => 'date',
        'class_time' => 'datetime',
    ];

    // Relationships
    public function convertedStudent()
    {
        return $this->belongsTo(ConvertedLead::class, 'converted_student_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
