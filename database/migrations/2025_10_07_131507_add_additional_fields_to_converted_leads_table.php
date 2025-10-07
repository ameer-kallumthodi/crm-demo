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
        Schema::table('converted_leads', function (Blueprint $table) {
            $table->string('dob')->nullable()->after('email');
            $table->string('username')->nullable()->after('dob');
            $table->text('password')->nullable()->after('username'); // encrypted
            $table->string('status')->nullable()->after('password'); // Paid, Admission cancel, etc.
            $table->string('reg_fee')->nullable()->after('status'); // Received, Not Received
            $table->string('exam_fee')->nullable()->after('reg_fee'); // Pending, Not Paid, Paid
            $table->string('ref_no')->nullable()->after('exam_fee');
            $table->string('enroll_no')->nullable()->after('ref_no');
            $table->string('id_card')->nullable()->after('enroll_no'); // Not Paid, Paid
            $table->string('tma')->nullable()->after('id_card'); // Not Paid, Paid
            $table->unsignedBigInteger('admission_batch_id')->nullable()->after('tma');
            
            $table->foreign('admission_batch_id')->references('id')->on('admission_batches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            $table->dropForeign(['admission_batch_id']);
            $table->dropColumn([
                'dob', 'username', 'password', 'status', 'reg_fee', 'exam_fee',
                'ref_no', 'enroll_no', 'id_card', 'tma', 'admission_batch_id'
            ]);
        });
    }
};
