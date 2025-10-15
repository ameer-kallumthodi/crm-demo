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
        Schema::table('leads', function (Blueprint $table) {
            // Single column indexes for frequently queried fields
            
            // Index for lead_status_id - used in filtering and status updates
            $table->index('lead_status_id', 'idx_leads_lead_status_id');
            
            // Index for lead_source_id - used in filtering
            $table->index('lead_source_id', 'idx_leads_lead_source_id');
            
            // Index for course_id - used in filtering and duplicate checking
            $table->index('course_id', 'idx_leads_course_id');
            
            // Index for telecaller_id - used in role-based filtering and assignments
            $table->index('telecaller_id', 'idx_leads_telecaller_id');
            
            // Index for team_id - used in team-based filtering
            $table->index('team_id', 'idx_leads_team_id');
            
            // Index for country_id - used in filtering
            $table->index('country_id', 'idx_leads_country_id');
            
            // Index for is_converted - used in filtering converted/non-converted leads
            $table->index('is_converted', 'idx_leads_is_converted');
            
            // Index for rating - used in filtering by rating
            $table->index('rating', 'idx_leads_rating');
            
            // Index for interest_status - used in filtering by interest level
            $table->index('interest_status', 'idx_leads_interest_status');
            
            // Index for created_at - used in date range filtering and ordering
            $table->index('created_at', 'idx_leads_created_at');
            
            // Index for followup_date - used in follow-up lead queries
            $table->index('followup_date', 'idx_leads_followup_date');
            
            // Index for deleted_at - used in soft delete queries
            $table->index('deleted_at', 'idx_leads_deleted_at');
            
            // Composite indexes for common query patterns
            
            // Composite index for date range filtering with status
            $table->index(['created_at', 'lead_status_id'], 'idx_leads_created_at_status');
            
            // Composite index for telecaller with date range
            $table->index(['telecaller_id', 'created_at'], 'idx_leads_telecaller_created_at');
            
            // Composite index for team with date range
            $table->index(['team_id', 'created_at'], 'idx_leads_team_created_at');
            
            // Composite index for course with status
            $table->index(['course_id', 'lead_status_id'], 'idx_leads_course_status');
            
            // Composite index for non-converted leads with date
            $table->index(['is_converted', 'created_at'], 'idx_leads_converted_created_at');
            
            // Composite index for follow-up leads (status = 2)
            $table->index(['lead_status_id', 'followup_date'], 'idx_leads_status_followup');
            
            // Composite index for duplicate checking (code + phone + course)
            $table->index(['code', 'phone', 'course_id'], 'idx_leads_duplicate_check');
            
            // Composite index for search functionality (title, phone, email)
            $table->index(['title'], 'idx_leads_title');
            $table->index(['phone'], 'idx_leads_phone');
            $table->index(['email'], 'idx_leads_email');
            
            // Composite index for bulk operations filtering
            $table->index(['lead_source_id', 'telecaller_id', 'created_at'], 'idx_leads_bulk_operations');
            
            // Composite index for overdue leads query
            $table->index(['is_converted', 'created_at', 'deleted_at'], 'idx_leads_overdue');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Drop all indexes in reverse order
            $table->dropIndex('idx_leads_overdue');
            $table->dropIndex('idx_leads_bulk_operations');
            $table->dropIndex('idx_leads_email');
            $table->dropIndex('idx_leads_phone');
            $table->dropIndex('idx_leads_title');
            $table->dropIndex('idx_leads_duplicate_check');
            $table->dropIndex('idx_leads_status_followup');
            $table->dropIndex('idx_leads_converted_created_at');
            $table->dropIndex('idx_leads_course_status');
            $table->dropIndex('idx_leads_team_created_at');
            $table->dropIndex('idx_leads_telecaller_created_at');
            $table->dropIndex('idx_leads_created_at_status');
            $table->dropIndex('idx_leads_deleted_at');
            $table->dropIndex('idx_leads_followup_date');
            $table->dropIndex('idx_leads_created_at');
            $table->dropIndex('idx_leads_interest_status');
            $table->dropIndex('idx_leads_rating');
            $table->dropIndex('idx_leads_is_converted');
            $table->dropIndex('idx_leads_country_id');
            $table->dropIndex('idx_leads_team_id');
            $table->dropIndex('idx_leads_telecaller_id');
            $table->dropIndex('idx_leads_course_id');
            $table->dropIndex('idx_leads_lead_source_id');
            $table->dropIndex('idx_leads_lead_status_id');
        });
    }
};
