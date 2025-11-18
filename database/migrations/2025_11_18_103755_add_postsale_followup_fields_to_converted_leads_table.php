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
            $table->date('postsale_followupdate')->nullable()->after('status');
            $table->time('postsale_followuptime')->nullable()->after('postsale_followupdate');
            $table->string('paid_status')->nullable()->after('postsale_followuptime');
            $table->string('call_status')->nullable()->after('paid_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            $table->dropColumn(['postsale_followupdate', 'postsale_followuptime', 'paid_status', 'call_status']);
        });
    }
};
