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
            $table->boolean('is_converted')->default(false)->after('meta_lead_id');
            $table->date('followup_date')->nullable()->after('is_converted');
            $table->text('remarks')->nullable()->after('followup_date');
            $table->string('code')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['is_converted', 'followup_date', 'remarks', 'code']);
        });
    }
};
