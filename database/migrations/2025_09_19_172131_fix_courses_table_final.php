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
        // First, update any null amount values to 0
        DB::table('courses')->whereNull('amount')->update(['amount' => 0]);
        
        Schema::table('courses', function (Blueprint $table) {
            // Check if columns exist before dropping them
            if (Schema::hasColumn('courses', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('courses', 'duration')) {
                $table->dropColumn('duration');
            }
            if (Schema::hasColumn('courses', 'fees')) {
                $table->dropColumn('fees');
            }
            
            // Change amount field to double and make it required
            $table->double('amount')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Add back the columns
            $table->text('description')->nullable();
            $table->string('duration')->nullable();
            $table->decimal('fees', 10, 2)->nullable();
            
            // Change amount back to decimal and nullable
            $table->decimal('amount', 10, 2)->nullable()->change();
        });
    }
};
