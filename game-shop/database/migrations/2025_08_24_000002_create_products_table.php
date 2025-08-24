<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['game_card', 'gift_card', 'subscription', 'uc_topup']);
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->foreignId('category_id')->constrained();
            $table->json('tags')->nullable();
            $table->json('country_availability')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_preorder')->default(false);
            $table->integer('stock_count')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
