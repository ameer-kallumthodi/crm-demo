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
            // Document verification status fields
            $table->enum('sslc_verification_status', ['pending', 'verified'])->default('pending')->after('sslc_certificate');
            $table->unsignedBigInteger('sslc_verified_by')->nullable()->after('sslc_verification_status');
            $table->timestamp('sslc_verified_at')->nullable()->after('sslc_verified_by');
            
            $table->enum('plustwo_verification_status', ['pending', 'verified'])->default('pending')->after('plustwo_certificate');
            $table->unsignedBigInteger('plustwo_verified_by')->nullable()->after('plustwo_verification_status');
            $table->timestamp('plustwo_verified_at')->nullable()->after('plustwo_verified_by');
            
            $table->enum('ug_verification_status', ['pending', 'verified'])->default('pending')->after('ug_certificate');
            $table->unsignedBigInteger('ug_verified_by')->nullable()->after('ug_verification_status');
            $table->timestamp('ug_verified_at')->nullable()->after('ug_verified_by');
            
            $table->enum('passport_photo_verification_status', ['pending', 'verified'])->default('pending')->after('passport_photo');
            $table->unsignedBigInteger('passport_photo_verified_by')->nullable()->after('passport_photo_verification_status');
            $table->timestamp('passport_photo_verified_at')->nullable()->after('passport_photo_verified_by');
            
            $table->enum('adhar_front_verification_status', ['pending', 'verified'])->default('pending')->after('adhar_front');
            $table->unsignedBigInteger('adhar_front_verified_by')->nullable()->after('adhar_front_verification_status');
            $table->timestamp('adhar_front_verified_at')->nullable()->after('adhar_front_verified_by');
            
            $table->enum('adhar_back_verification_status', ['pending', 'verified'])->default('pending')->after('adhar_back');
            $table->unsignedBigInteger('adhar_back_verified_by')->nullable()->after('adhar_back_verification_status');
            $table->timestamp('adhar_back_verified_at')->nullable()->after('adhar_back_verified_by');
            
            $table->enum('signature_verification_status', ['pending', 'verified'])->default('pending')->after('signature');
            $table->unsignedBigInteger('signature_verified_by')->nullable()->after('signature_verification_status');
            $table->timestamp('signature_verified_at')->nullable()->after('signature_verified_by');
            
            // Foreign key constraints
            $table->foreign('sslc_verified_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('plustwo_verified_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('ug_verified_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('passport_photo_verified_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('adhar_front_verified_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('adhar_back_verified_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('signature_verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads_details', function (Blueprint $table) {
            $table->dropForeign(['sslc_verified_by']);
            $table->dropForeign(['plustwo_verified_by']);
            $table->dropForeign(['ug_verified_by']);
            $table->dropForeign(['passport_photo_verified_by']);
            $table->dropForeign(['adhar_front_verified_by']);
            $table->dropForeign(['adhar_back_verified_by']);
            $table->dropForeign(['signature_verified_by']);
            
            $table->dropColumn([
                'sslc_verification_status', 'sslc_verified_by', 'sslc_verified_at',
                'plustwo_verification_status', 'plustwo_verified_by', 'plustwo_verified_at',
                'ug_verification_status', 'ug_verified_by', 'ug_verified_at',
                'passport_photo_verification_status', 'passport_photo_verified_by', 'passport_photo_verified_at',
                'adhar_front_verification_status', 'adhar_front_verified_by', 'adhar_front_verified_at',
                'adhar_back_verification_status', 'adhar_back_verified_by', 'adhar_back_verified_at',
                'signature_verification_status', 'signature_verified_by', 'signature_verified_at',
            ]);
        });
    }
};
