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
            Schema::create('free_fire_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('player_id');
            $table->integer('diamond_amount');
            $table->decimal('price', 10, 2);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed']);
            $table->string('transaction_id')->nullable();
            $table->string('redemption_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('free_fire_orders');
    }
};
