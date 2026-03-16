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
            $table->unsignedBigInteger('post_sales_user_id')->nullable()->after('post_sales_remarks');
            $table->foreign('post_sales_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            $table->dropForeign(['post_sales_user_id']);
            $table->dropColumn('post_sales_user_id');
        });
    }
};
