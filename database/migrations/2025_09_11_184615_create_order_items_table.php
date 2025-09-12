<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->string('product_name');
            $table->string('product_api_id');
            $table->integer('quantity');
            $table->decimal('cost_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->json('optional_fields_data')->nullable();
            $table->json('serials')->nullable();
            $table->string('serial_code')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('valid_to')->nullable();
            $table->enum('status', ['pending', 'delivered', 'failed'])->default('pending');
            $table->timestamps();

            $table->index('order_id');
            $table->index('product_id');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};
