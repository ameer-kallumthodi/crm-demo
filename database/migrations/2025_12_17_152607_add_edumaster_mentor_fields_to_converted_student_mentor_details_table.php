<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableName = 'converted_student_mentor_details';
        
        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            // SSLC Back Year fields
            if (!Schema::hasColumn($tableName, 'sslc_enrollment_number')) {
                $table->string('sslc_enrollment_number')->nullable()->after('remarks_internal_notes');
            }
            if (!Schema::hasColumn($tableName, 'sslc_registration_link_id')) {
                $table->unsignedBigInteger('sslc_registration_link_id')->nullable()->after('sslc_enrollment_number');
            }
            if (!Schema::hasColumn($tableName, 'sslc_online_result_publication_date')) {
                $table->date('sslc_online_result_publication_date')->nullable()->after('sslc_registration_link_id');
            }
            if (!Schema::hasColumn($tableName, 'sslc_certificate_publication_date')) {
                $table->date('sslc_certificate_publication_date')->nullable()->after('sslc_online_result_publication_date');
            }
            if (!Schema::hasColumn($tableName, 'sslc_certificate_issued_date')) {
                $table->date('sslc_certificate_issued_date')->nullable()->after('sslc_certificate_publication_date');
            }
            if (!Schema::hasColumn($tableName, 'sslc_certificate_distribution_date')) {
                $table->date('sslc_certificate_distribution_date')->nullable()->after('sslc_certificate_issued_date');
            }
            if (!Schema::hasColumn($tableName, 'sslc_courier_tracking_number')) {
                $table->string('sslc_courier_tracking_number')->nullable()->after('sslc_certificate_distribution_date');
            }
            if (!Schema::hasColumn($tableName, 'sslc_remarks')) {
                $table->text('sslc_remarks')->nullable()->after('sslc_courier_tracking_number');
            }
            
            // Plus Two Back Year fields
            if (!Schema::hasColumn($tableName, 'plustwo_subject_no')) {
                $table->string('plustwo_subject_no')->nullable()->after('sslc_remarks');
            }
            if (!Schema::hasColumn($tableName, 'plustwo_enrollment_number')) {
                $table->string('plustwo_enrollment_number')->nullable()->after('plustwo_subject_no');
            }
            if (!Schema::hasColumn($tableName, 'plustwo_registration_link_id')) {
                $table->unsignedBigInteger('plustwo_registration_link_id')->nullable()->after('plustwo_enrollment_number');
            }
            if (!Schema::hasColumn($tableName, 'plustwo_online_result_publication_date')) {
                $table->date('plustwo_online_result_publication_date')->nullable()->after('plustwo_registration_link_id');
            }
            if (!Schema::hasColumn($tableName, 'plustwo_certificate_publication_date')) {
                $table->date('plustwo_certificate_publication_date')->nullable()->after('plustwo_online_result_publication_date');
            }
            if (!Schema::hasColumn($tableName, 'plustwo_certificate_issued_date')) {
                $table->date('plustwo_certificate_issued_date')->nullable()->after('plustwo_certificate_publication_date');
            }
            if (!Schema::hasColumn($tableName, 'plustwo_certificate_distribution_date')) {
                $table->date('plustwo_certificate_distribution_date')->nullable()->after('plustwo_certificate_issued_date');
            }
            if (!Schema::hasColumn($tableName, 'plustwo_courier_tracking_number')) {
                $table->string('plustwo_courier_tracking_number')->nullable()->after('plustwo_certificate_distribution_date');
            }
            if (!Schema::hasColumn($tableName, 'plustwo_remarks')) {
                $table->text('plustwo_remarks')->nullable()->after('plustwo_courier_tracking_number');
            }
            
            // Degree Back Year fields
            if (!Schema::hasColumn($tableName, 'degree_board_university')) {
                $table->string('degree_board_university')->nullable()->after('plustwo_remarks');
            }
            if (!Schema::hasColumn($tableName, 'degree_course_type')) {
                $table->string('degree_course_type')->nullable()->after('degree_board_university');
            }
            if (!Schema::hasColumn($tableName, 'degree_course_name')) {
                $table->string('degree_course_name')->nullable()->after('degree_course_type');
            }
            if (!Schema::hasColumn($tableName, 'degree_back_year')) {
                $table->string('degree_back_year')->nullable()->after('degree_course_name');
            }
            if (!Schema::hasColumn($tableName, 'degree_registration_start_date')) {
                $table->date('degree_registration_start_date')->nullable()->after('degree_back_year');
            }
            if (!Schema::hasColumn($tableName, 'degree_registration_form_summary_distribution_date')) {
                $table->date('degree_registration_form_summary_distribution_date')->nullable()->after('degree_registration_start_date');
            }
            if (!Schema::hasColumn($tableName, 'degree_registration_form_summary_submission_date')) {
                $table->date('degree_registration_form_summary_submission_date')->nullable()->after('degree_registration_form_summary_distribution_date');
            }
            if (!Schema::hasColumn($tableName, 'degree_id_card_issued_date')) {
                $table->date('degree_id_card_issued_date')->nullable()->after('degree_registration_form_summary_submission_date');
            }
            if (!Schema::hasColumn($tableName, 'degree_first_year_result_date')) {
                $table->date('degree_first_year_result_date')->nullable()->after('degree_id_card_issued_date');
            }
            if (!Schema::hasColumn($tableName, 'degree_second_year_result_date')) {
                $table->date('degree_second_year_result_date')->nullable()->after('degree_first_year_result_date');
            }
            if (!Schema::hasColumn($tableName, 'degree_third_year_result_date')) {
                $table->date('degree_third_year_result_date')->nullable()->after('degree_second_year_result_date');
            }
            if (!Schema::hasColumn($tableName, 'degree_online_result_publication_date')) {
                $table->date('degree_online_result_publication_date')->nullable()->after('degree_third_year_result_date');
            }
            if (!Schema::hasColumn($tableName, 'degree_certificate_publication_date')) {
                $table->date('degree_certificate_publication_date')->nullable()->after('degree_online_result_publication_date');
            }
            if (!Schema::hasColumn($tableName, 'degree_certificate_issued_date')) {
                $table->date('degree_certificate_issued_date')->nullable()->after('degree_certificate_publication_date');
            }
            if (!Schema::hasColumn($tableName, 'degree_certificate_distribution_date')) {
                $table->date('degree_certificate_distribution_date')->nullable()->after('degree_certificate_issued_date');
            }
            if (!Schema::hasColumn($tableName, 'degree_courier_tracking_number')) {
                $table->string('degree_courier_tracking_number')->nullable()->after('degree_certificate_distribution_date');
            }
            if (!Schema::hasColumn($tableName, 'degree_remarks')) {
                $table->text('degree_remarks')->nullable()->after('degree_courier_tracking_number');
            }
        });
        
        // Add foreign keys separately, checking if they don't already exist
        if (Schema::hasColumn($tableName, 'sslc_registration_link_id')) {
            try {
                // Check if foreign key constraint already exists by trying to get it
                $constraintExists = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = ? 
                    AND CONSTRAINT_NAME = ?
                ", [$tableName, 'csmd_sslc_reg_link_fk']);
                
                if (empty($constraintExists)) {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->foreign('sslc_registration_link_id', 'csmd_sslc_reg_link_fk')->references('id')->on('registration_links')->onDelete('set null');
                    });
                }
            } catch (\Exception $e) {
                // Constraint might already exist, ignore
            }
        }
        
        if (Schema::hasColumn($tableName, 'plustwo_registration_link_id')) {
            try {
                // Check if foreign key constraint already exists by trying to get it
                $constraintExists = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = ? 
                    AND CONSTRAINT_NAME = ?
                ", [$tableName, 'csmd_plustwo_reg_link_fk']);
                
                if (empty($constraintExists)) {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->foreign('plustwo_registration_link_id', 'csmd_plustwo_reg_link_fk')->references('id')->on('registration_links')->onDelete('set null');
                    });
                }
            } catch (\Exception $e) {
                // Constraint might already exist, ignore
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_student_mentor_details', function (Blueprint $table) {
            $table->dropForeign('csmd_sslc_reg_link_fk');
            $table->dropForeign('csmd_plustwo_reg_link_fk');
            
            $table->dropColumn([
                'sslc_enrollment_number',
                'sslc_registration_link_id',
                'sslc_online_result_publication_date',
                'sslc_certificate_publication_date',
                'sslc_certificate_issued_date',
                'sslc_certificate_distribution_date',
                'sslc_courier_tracking_number',
                'sslc_remarks',
                'plustwo_subject_no',
                'plustwo_enrollment_number',
                'plustwo_registration_link_id',
                'plustwo_online_result_publication_date',
                'plustwo_certificate_publication_date',
                'plustwo_certificate_issued_date',
                'plustwo_certificate_distribution_date',
                'plustwo_courier_tracking_number',
                'plustwo_remarks',
                'degree_board_university',
                'degree_course_type',
                'degree_course_name',
                'degree_back_year',
                'degree_registration_start_date',
                'degree_registration_form_summary_distribution_date',
                'degree_registration_form_summary_submission_date',
                'degree_id_card_issued_date',
                'degree_first_year_result_date',
                'degree_second_year_result_date',
                'degree_third_year_result_date',
                'degree_online_result_publication_date',
                'degree_certificate_publication_date',
                'degree_certificate_issued_date',
                'degree_certificate_distribution_date',
                'degree_courier_tracking_number',
                'degree_remarks',
            ]);
        });
    }
};
