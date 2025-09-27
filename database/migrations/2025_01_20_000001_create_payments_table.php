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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->decimal('amount_paid', 10, 2);
            $table->enum('payment_type', ['Cash', 'Online', 'Bank', 'Cheque', 'Card', 'Other'])->default('Cash');
            $table->string('transaction_id')->nullable();
            $table->string('file_upload')->nullable();
            $table->enum('status', ['Pending Approval', 'Approved', 'Rejected'])->default('Pending Approval');
            
            // Audit fields
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('cascade');
            
            // Soft deletes and timestamps
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index(['invoice_id', 'status']);
            $table->index('payment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
