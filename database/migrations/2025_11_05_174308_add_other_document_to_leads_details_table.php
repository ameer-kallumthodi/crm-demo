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
            // Add other_document field after signature
            if (!Schema::hasColumn('leads_details', 'other_document')) {
                $table->string('other_document')->nullable()->after('signature');
            }
            
            // Add verification fields for other_document
            if (!Schema::hasColumn('leads_details', 'other_document_verification_status')) {
                $table->enum('other_document_verification_status', ['pending', 'verified'])->default('pending')->after('signature_verified_at');
            }
            
            if (!Schema::hasColumn('leads_details', 'other_document_verified_by')) {
                $table->unsignedBigInteger('other_document_verified_by')->nullable()->after('other_document_verification_status');
            }
            
            if (!Schema::hasColumn('leads_details', 'other_document_verified_at')) {
                $table->timestamp('other_document_verified_at')->nullable()->after('other_document_verified_by');
            }
        });
        
        // Add foreign key separately to avoid issues
        Schema::table('leads_details', function (Blueprint $table) {
            if (Schema::hasColumn('leads_details', 'other_document_verified_by')) {
                try {
                    $table->foreign('other_document_verified_by')->references('id')->on('users')->onDelete('set null');
                } catch (\Exception $e) {
                    // Foreign key might already exist, ignore
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads_details', function (Blueprint $table) {
            // Drop foreign key first
            try {
                $table->dropForeign(['other_document_verified_by']);
            } catch (\Exception $e) {
                // Foreign key might not exist, ignore
            }
            
            // Drop columns
            $columnsToDrop = [];
            if (Schema::hasColumn('leads_details', 'other_document_verified_at')) {
                $columnsToDrop[] = 'other_document_verified_at';
            }
            if (Schema::hasColumn('leads_details', 'other_document_verified_by')) {
                $columnsToDrop[] = 'other_document_verified_by';
            }
            if (Schema::hasColumn('leads_details', 'other_document_verification_status')) {
                $columnsToDrop[] = 'other_document_verification_status';
            }
            if (Schema::hasColumn('leads_details', 'other_document')) {
                $columnsToDrop[] = 'other_document';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
