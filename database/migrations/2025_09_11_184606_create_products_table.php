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
            $table->string('api_id')->unique();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('cost_price', 10, 2); // Price from API
            $table->decimal('selling_price', 10, 2); // Price with margin
            $table->decimal('margin_amount', 10, 2)->default(0);
            $table->decimal('margin_percentage', 5, 2)->default(0);
            $table->enum('margin_type', ['fixed', 'percentage'])->default('percentage');
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_available')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('stock_quantity')->nullable();
            $table->json('optional_fields')->nullable();
            $table->json('forbidden_countries')->nullable();
            $table->text('redemption_instructions')->nullable();
            $table->integer('sort_order')->default(0);
            $table->integer('sales_count')->default(0);
            $table->decimal('vat_percentage', 5, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('category_id');
            $table->index('slug');
            $table->index('is_active');
            $table->index('is_available');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
