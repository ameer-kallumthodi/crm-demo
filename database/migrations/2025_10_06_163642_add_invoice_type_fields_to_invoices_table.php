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
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('invoice_type', ['course', 'e-service', 'batch_change'])->default('course')->after('invoice_number');
            $table->foreignId('batch_id')->nullable()->constrained('batches')->onDelete('cascade')->after('course_id');
            $table->string('service_name')->nullable()->after('batch_id');
            $table->decimal('service_amount', 10, 2)->nullable()->after('service_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn(['invoice_type', 'batch_id', 'service_name', 'service_amount']);
        });
    }
};