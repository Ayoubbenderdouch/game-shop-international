<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pubg_uc_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('player_id');
            $table->integer('uc_amount');
            $table->decimal('price', 10, 2);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed']);
            $table->string('transaction_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pubg_uc_orders');
    }
};
