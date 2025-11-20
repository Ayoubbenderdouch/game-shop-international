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
        Schema::create('mazaya_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('mazaya_order_id')->nullable();
            $table->string('uuid')->nullable()->unique();

            // Product info
            $table->integer('mazaya_product_id');
            $table->string('product_name');
            $table->string('game_name')->nullable();

            // Player info
            $table->string('player_id');
            $table->string('player_name')->nullable();

            // Order details
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'canceled'])->default('pending');

            // Customer data from Mazaya
            $table->json('customer_data')->nullable();

            // Admin data from Mazaya (codes, messages)
            $table->json('admin_data')->nullable();

            // Response data
            $table->text('response_message')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('mazaya_order_id');
            $table->index('uuid');
            $table->index('status');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mazaya_orders');
    }
};
