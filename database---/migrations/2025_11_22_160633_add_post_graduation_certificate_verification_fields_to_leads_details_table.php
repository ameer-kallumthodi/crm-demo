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
        Schema::table('leads_details', function (Blueprint $table) {
            $table->enum('post_graduation_certificate_verification_status', ['pending', 'verified'])->default('pending')->after('post_graduation_certificate');
            $table->unsignedBigInteger('post_graduation_certificate_verified_by')->nullable()->after('post_graduation_certificate_verification_status');
            $table->timestamp('post_graduation_certificate_verified_at')->nullable()->after('post_graduation_certificate_verified_by');
            
            // Foreign key
            $table->foreign('post_graduation_certificate_verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads_details', function (Blueprint $table) {
            $table->dropForeign(['post_graduation_certificate_verified_by']);
            $table->dropColumn([
                'post_graduation_certificate_verification_status',
                'post_graduation_certificate_verified_by',
                'post_graduation_certificate_verified_at'
            ]);
        });
    }
};
