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
            $table->enum('birth_certificate_verification_status', ['pending', 'verified'])->default('pending')->after('signature_verified_at');
            $table->unsignedBigInteger('birth_certificate_verified_by')->nullable()->after('birth_certificate_verification_status');
            $table->timestamp('birth_certificate_verified_at')->nullable()->after('birth_certificate_verified_by');
            
            // Foreign key
            $table->foreign('birth_certificate_verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads_details', function (Blueprint $table) {
            $table->dropForeign(['birth_certificate_verified_by']);
            $table->dropColumn([
                'birth_certificate_verification_status',
                'birth_certificate_verified_by',
                'birth_certificate_verified_at'
            ]);
        });
    }
};

