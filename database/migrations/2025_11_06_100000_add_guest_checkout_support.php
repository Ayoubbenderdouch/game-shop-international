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
        Schema::table('orders', function (Blueprint $table) {
            // Make user_id nullable for guest orders
            $table->unsignedBigInteger('user_id')->nullable()->change();
            
            // Add guest customer fields
            $table->string('guest_email')->nullable()->after('user_id');
            $table->string('guest_name')->nullable()->after('guest_email');
            $table->string('guest_phone')->nullable()->after('guest_name');
            
            // Add payment transaction ID
            $table->string('payment_transaction_id')->nullable()->after('payment_method');
            
            // Add index for guest email to track orders
            $table->index('guest_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['guest_email', 'guest_name', 'guest_phone', 'payment_transaction_id']);
            $table->dropIndex(['guest_email']);
            
            // Note: Reverting user_id to non-nullable might fail if guest orders exist
            // Handle manually if needed
        });
    }
};
