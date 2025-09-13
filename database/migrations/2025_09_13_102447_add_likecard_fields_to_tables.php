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
        // Add last_synced_at column to categories table if it doesn't exist
        if (Schema::hasTable('categories') && !Schema::hasColumn('categories', 'last_synced_at')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->timestamp('last_synced_at')->nullable()->after('updated_at');
                $table->index('last_synced_at');
            });
        }

        // Add api_id index if it doesn't exist
        if (Schema::hasTable('categories') && !Schema::hasColumn('categories', 'api_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('api_id')->nullable()->after('id');
                $table->index('api_id');
                $table->unique('api_id');
            });
        }

        // Add api_id to products table if it doesn't exist
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'api_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('api_id')->nullable()->after('id');
                $table->index('api_id');
                $table->unique('api_id');
            });
        }

        // Add optional_fields column to products table if it doesn't exist
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'optional_fields')) {
            Schema::table('products', function (Blueprint $table) {
                $table->json('optional_fields')->nullable()->after('metadata');
            });
        }

        // Add currency column to products table if it doesn't exist
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'currency')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('currency', 10)->default('USD')->after('selling_price');
            });
        }

        // Add vat_percentage column to products table if it doesn't exist
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'vat_percentage')) {
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('vat_percentage', 5, 2)->default(0)->after('currency');
            });
        }

        // Add api_order_id to orders table if it doesn't exist
        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'api_order_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('api_order_id')->nullable()->after('order_number');
                $table->string('api_reference_id')->nullable()->after('api_order_id');
                $table->index('api_order_id');
                $table->index('api_reference_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn(['last_synced_at']);
            });
        }
    }
};
