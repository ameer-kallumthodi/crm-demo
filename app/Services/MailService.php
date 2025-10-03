<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\StudentDetail;
use Carbon\Carbon;

class MailService
{
    public static function sendStudentRegistrationEmail($student, $courseType)
    {
        $subject = "Registration Confirmation – {$courseType} Course";
        
        $body = self::buildStudentRegistrationEmailBody($student, $courseType);
        
        // Get attachments
        $attachments = self::getStudentAttachments($student);
        
        // Send to student
        send_email($student->email, $student->name, $subject, $body, $attachments);
        
        // Send copy to CAO
        send_email('cao@natdemy.com', 'CAO', $subject, $body, $attachments);
    }
    
    public static function sendNiosStudentVerificationEmail($student, $verifier)
    {
        $subject = "🎓 NIOS Student Verified: {$student->name}";
        
        $body = self::buildVerificationEmailBody($student, $verifier);
        
        // Get attachments
        $attachments = self::getStudentAttachments($student);
        
        // Send to verifier
        send_email($verifier->email, $verifier->name, $subject, $body, $attachments);
        
        // Send copy to CAO
        send_email('cao@natdemy.com', 'CAO', $subject, $body, $attachments);
    }
    
    private static function buildStudentRegistrationEmailBody($student, $courseType)
    {
        // Build Basic Info list with only available fields
        $basicItems = [];
        if (!empty($student->student_name)) { $basicItems[] = "<li><b>Name:</b> {$student->student_name}</li>"; }
        if (!empty($student->father_name)) { $basicItems[] = "<li><b>Father Name:</b> {$student->father_name}</li>"; }
        if (!empty($student->mother_name)) { $basicItems[] = "<li><b>Mother Name:</b> {$student->mother_name}</li>"; }
        if (!empty($student->date_of_birth)) { $basicItems[] = "<li><b>Date of Birth:</b> {$student->date_of_birth}</li>"; }
        if (!empty($student->email)) { $basicItems[] = "<li><b>Email:</b> {$student->email}</li>"; }
        if (!empty($student->personal_number)) { $basicItems[] = "<li><b>Personal Number:</b> {$student->personal_number}</li>"; }
        if (!empty($student->parents_number)) { $basicItems[] = "<li><b>Parents Number:</b> {$student->parents_number}</li>"; }
        if (!empty($student->whatsapp_number)) { $basicItems[] = "<li><b>WhatsApp Number:</b> {$student->whatsapp_number}</li>"; }
        $basicHtml = implode("\n", $basicItems);

        // Resolve subject and batch names from relation or string fields
        $subjectName = null;
        if (isset($student->subject)) {
            $subjectName = $student->subject->title ?? $student->subject->name ?? null;
        }
        if (empty($subjectName) && !empty($student->subject_name)) {
            $subjectName = $student->subject_name;
        }

        $batchName = null;
        if (isset($student->batch)) {
            $batchName = $student->batch->title ?? $student->batch->name ?? null;
        }
        if (empty($batchName) && !empty($student->batch_name)) {
            $batchName = $student->batch_name;
        }

        // Build Course Info list with only available fields
        $courseItems = [];
        if (!empty($courseType)) { $courseItems[] = "<li><b>Course:</b> {$courseType}</li>"; }
        if (!empty($subjectName)) { $courseItems[] = "<li><b>Subject:</b> {$subjectName}</li>"; }
        if (!empty($batchName)) { $courseItems[] = "<li><b>Batch:</b> {$batchName}</li>"; }
        if (!empty($student->second_language)) { $courseItems[] = "<li><b>Second Language:</b> {$student->second_language}</li>"; }
        $courseHtml = implode("\n", $courseItems);

        // Build Address list with only available fields
        $addressItems = [];
        if (!empty($student->street)) { $addressItems[] = "<li><b>Street:</b> {$student->street}</li>"; }
        if (!empty($student->locality)) { $addressItems[] = "<li><b>Locality:</b> {$student->locality}</li>"; }
        if (!empty($student->post_office)) { $addressItems[] = "<li><b>Post Office:</b> {$student->post_office}</li>"; }
        if (!empty($student->district)) { $addressItems[] = "<li><b>District:</b> {$student->district}</li>"; }
        if (!empty($student->state)) { $addressItems[] = "<li><b>State:</b> {$student->state}</li>"; }
        if (!empty($student->pin_code)) { $addressItems[] = "<li><b>PIN Code:</b> {$student->pin_code}</li>"; }
        $addressHtml = implode("\n", $addressItems);

        return "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 700px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>
                
                <h2 style='color: #2c3e50; text-align: center;'>Registration Confirmation – {$courseType} Course</h2>
                <p>Dear <b>{$student->student_name}</b>,</p>

                <p>Thank you for registering with Skill Park! We have received your application and documents for the {$courseType} course.</p>

                <hr style='margin:20px 0;'>

                <h3 style='color: #2c3e50;'>📌 Your Registration Details</h3>
                <ul>
                    {$basicHtml}
                </ul>

                <h3 style='color: #2c3e50;'>📌 Course Information</h3>
                <ul>
                    {$courseHtml}
                </ul>

                <h3 style='color: #2c3e50;'>📌 Address</h3>
                <ul>
                    {$addressHtml}
                </ul>

                <p><b>Registration Date:</b> " . now()->format('d-m-Y H:i:s') . "</p>

                <hr style='margin:20px 0;'>
                <p>
                    We have received all your documents and will review them within 24 hours. 
                    Our team will contact you soon regarding the next steps in your admission process.
                </p>

                <p>
                    If you have any questions, please don't hesitate to contact us at 
                    <a href='mailto:support@skill-park.com'>support@skill-park.com</a>
                </p>

                <p style='margin-top:30px;'>
                    Best regards,<br>
                    <b>Skill Park Team</b><br>
                    <a href='mailto:support@skill-park.com'>support@skill-park.com</a>
                </p>
            </div>
        </body>
        </html>";
    }
    
    private static function buildVerificationEmailBody($student, $verifier)
    {
        // Build Basic Info list with only available fields
        $basicItems = [];
        if (!empty($student->student_name)) { $basicItems[] = "<li><b>Name:</b> {$student->student_name}</li>"; }
        if (!empty($student->father_name)) { $basicItems[] = "<li><b>Father Name:</b> {$student->father_name}</li>"; }
        if (!empty($student->mother_name)) { $basicItems[] = "<li><b>Mother Name:</b> {$student->mother_name}</li>"; }
        if (!empty($student->date_of_birth)) { $basicItems[] = "<li><b>Date of Birth:</b> {$student->date_of_birth}</li>"; }
        if (!empty($student->email)) { $basicItems[] = "<li><b>Email:</b> {$student->email}</li>"; }
        if (!empty($student->personal_number)) { $basicItems[] = "<li><b>Personal Number:</b> {$student->personal_number}</li>"; }
        if (!empty($student->parents_number)) { $basicItems[] = "<li><b>Parents Number:</b> {$student->parents_number}</li>"; }
        if (!empty($student->whatsapp_number)) { $basicItems[] = "<li><b>WhatsApp Number:</b> {$student->whatsapp_number}</li>"; }
        $basicHtml = implode("\n", $basicItems);

        // Resolve subject and batch names from relation or string fields
        $subjectName = null;
        if (isset($student->subject)) {
            $subjectName = $student->subject->title ?? $student->subject->name ?? null;
        }
        if (empty($subjectName) && !empty($student->subject_name)) {
            $subjectName = $student->subject_name;
        }

        $batchName = null;
        if (isset($student->batch)) {
            $batchName = $student->batch->title ?? $student->batch->name ?? null;
        }
        if (empty($batchName) && !empty($student->batch_name)) {
            $batchName = $student->batch_name;
        }

        // Build Educational Details list with only available fields
        $educationItems = [];
        $educationItems[] = "<li><b>Course:</b> NIOS</li>";
        if (!empty($subjectName)) { $educationItems[] = "<li><b>Subject:</b> {$subjectName}</li>"; }
        if (!empty($batchName)) { $educationItems[] = "<li><b>Batch:</b> {$batchName}</li>"; }
        if (!empty($student->second_language)) { $educationItems[] = "<li><b>Second Language:</b> {$student->second_language}</li>"; }
        $educationHtml = implode("\n", $educationItems);

        // Build Address list with only available fields
        $addressItems = [];
        if (!empty($student->street)) { $addressItems[] = "<li><b>Street:</b> {$student->street}</li>"; }
        if (!empty($student->locality)) { $addressItems[] = "<li><b>Locality:</b> {$student->locality}</li>"; }
        if (!empty($student->post_office)) { $addressItems[] = "<li><b>Post Office:</b> {$student->post_office}</li>"; }
        if (!empty($student->district)) { $addressItems[] = "<li><b>District:</b> {$student->district}</li>"; }
        if (!empty($student->state)) { $addressItems[] = "<li><b>State:</b> {$student->state}</li>"; }
        if (!empty($student->pin_code)) { $addressItems[] = "<li><b>PIN Code:</b> {$student->pin_code}</li>"; }
        $addressHtml = implode("\n", $addressItems);
        
        return "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 700px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>
                
                <h2 style='color: #2c3e50; text-align: center;'>Admission Verification Confirmation – Official Record Copy</h2>
                <p>Dear <b>{$verifier->name}</b>,</p>

                <p>This is to officially confirm that the student data and supporting documents verified by you 
                have now been recorded in the admission system.</p>

                <hr style='margin:20px 0;'>

                <h3 style='color: #2c3e50;'>📌 Step 1: Basic Info</h3>
                <ul>
                    {$basicHtml}
                </ul>

                <h3 style='color: #2c3e50;'>📌 Step 2: Educational Details</h3>
                <ul>
                    {$educationHtml}
                </ul>

                <h3 style='color: #2c3e50;'>📌 Step 3: Address</h3>
                <ul>
                    {$addressHtml}
                </ul>

                <p><b>Verified By:</b> {$verifier->name}<br>
                <b>Verified At:</b> " . now()->format('d-m-Y H:i:s') . "</p>

                <hr style='margin:20px 0;'>
                <p>
                    This is to officially confirm that the student data and supporting documents
                    verified by you have now been recorded in the admission system. The details contained
                    in this record are based on the submissions made by the student and your verification
                    as the responsible officer.
                </p>

                <p>
                    By completing this task, you acknowledge that all the student's information, including personal details,
                    identification proof, academic certificates, and any other required documents,
                    have been thoroughly checked by you with due diligence. You further confirm that the 
                    verification has been carried out in accordance with the institutional guidelines and 
                    standards of accuracy.
                </p>

                <p>
                    Please be reminded that the responsibility for this verification rests entirely with you as the verifying
                    officer. In the event of any discrepancies, errors, or issues arising in the future regarding
                    this student's data or documents, accountability will remain under your role as the 
                    verifier.
                </p>

                <p>
                    We request you to retain this confirmation as part of your official record for future reference. 
                    Your diligence and professionalism in performing this responsibility are greatly appreciated by the Academic 
                    Admission Department.
                </p>

                <p style='margin-top:30px;'>
                    Sincerely,<br>
                    <b>Academic Admission Department</b>
                </p>
            </div>
        </body>
        </html>";
    }
    
    private static function getStudentAttachments($student)
    {
        $attachments = [];
        
        // All document fields from leads_details table
        $documentFields = [
            'birth_certificate',
            'passport_photo',
            'adhar_front',
            'adhar_back',
            'signature',
            'plustwo_certificate',
            'ug_certificate',
            'sslc_certificate'
        ];

        foreach ($documentFields as $field) {
            if (!empty($student->$field)) {
                $filePath = storage_path('app/public/' . $student->$field);
                if (file_exists($filePath)) {
                    $attachments[] = $filePath;
                }
            }
        }

        return $attachments;
    }
    
    private static function getFieldName($field)
    {
        if (is_string($field)) {
            return $field;
        } elseif (is_object($field)) {
            // Handle Eloquent models or objects
            if (isset($field->title)) {
                return $field->title;
            } elseif (isset($field->name)) {
                return $field->name;
            } elseif (method_exists($field, 'toArray')) {
                $array = $field->toArray();
                return $array['title'] ?? $array['name'] ?? 'N/A';
            }
        } elseif (is_array($field)) {
            return $field['title'] ?? $field['name'] ?? 'N/A';
        }
        
        return 'N/A';
    }
}
