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
        // Update enum to include Razorpay
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_type ENUM('Cash', 'Online', 'Bank', 'Cheque', 'Card', 'Other', 'Razorpay') DEFAULT 'Cash'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert enum back to original
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_type ENUM('Cash', 'Online', 'Bank', 'Cheque', 'Card', 'Other') DEFAULT 'Cash'");
    }
};
