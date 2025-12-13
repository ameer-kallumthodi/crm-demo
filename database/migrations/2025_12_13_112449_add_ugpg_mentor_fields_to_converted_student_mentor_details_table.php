<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            // UG/PG Mentor specific fields
            $table->date('online_registration_date')->nullable()->after('exam_subject_6');
            $table->date('admission_form_issued_date')->nullable()->after('online_registration_date');
            $table->date('admission_form_returned_date')->nullable()->after('admission_form_issued_date');
            $table->enum('document_verification_status', ['Not Verified', 'Verified'])->nullable()->after('admission_form_returned_date');
            $table->date('verification_completed_date')->nullable()->after('document_verification_status');
            $table->date('id_card_issued_date')->nullable()->after('verification_completed_date');
            $table->date('first_year_result_declaration_date')->nullable()->after('id_card_issued_date');
            $table->date('second_year_result_declaration_date')->nullable()->after('first_year_result_declaration_date');
            $table->date('third_year_result_declaration_date')->nullable()->after('second_year_result_declaration_date');
            $table->date('all_online_result_publication_date')->nullable()->after('third_year_result_declaration_date');
            $table->date('certificate_issued_date')->nullable()->after('all_online_result_publication_date');
            $table->enum('certificate_distribution_mode', ['In Person', 'Courier'])->nullable()->after('certificate_issued_date');
            $table->string('courier_tracking_number')->nullable()->after('certificate_distribution_mode');
            // Update student_status enum to include UG/PG values (keeping existing values for backward compatibility)
            $table->enum('student_status', ['Low Level', 'Below Medium', 'Medium Level', 'Advanced Level', 'Active', 'Completed', 'Discontinued'])->nullable()->change();
            $table->text('remarks_internal_notes')->nullable()->after('courier_tracking_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            $table->dropColumn([
                'online_registration_date',
                'admission_form_issued_date',
                'admission_form_returned_date',
                'document_verification_status',
                'verification_completed_date',
                'id_card_issued_date',
                'first_year_result_declaration_date',
                'second_year_result_declaration_date',
                'third_year_result_declaration_date',
                'all_online_result_publication_date',
                'certificate_issued_date',
                'certificate_distribution_mode',
                'courier_tracking_number',
                'remarks_internal_notes',
            ]);
            // Note: student_status enum change cannot be easily reverted, so we leave it
        });
    }
};
