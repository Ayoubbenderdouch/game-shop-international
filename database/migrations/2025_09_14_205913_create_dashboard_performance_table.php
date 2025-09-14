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
        // Add indexes to orders table for better dashboard performance
        Schema::table('orders', function (Blueprint $table) {
            // Check if indexes don't already exist before creating them
            $existingIndexes = $this->getTableIndexes('orders');

            // Index for date-based queries
            if (!in_array('created_at', $existingIndexes)) {
                $table->index('created_at', 'idx_orders_created_at');
            }

            // Index for payment status filtering (might already exist from original migration)
            if (!in_array('payment_status', $existingIndexes)) {
                $table->index('payment_status', 'idx_orders_payment_status_perf');
            }

            // Index for order status filtering (might already exist from original migration)
            if (!in_array('status', $existingIndexes)) {
                $table->index('status', 'idx_orders_status_perf');
            }

            // Composite index for common dashboard queries
            if (!$this->compositeIndexExists('orders', ['payment_status', 'created_at'])) {
                $table->index(['payment_status', 'created_at'], 'idx_orders_payment_created');
            }
        });

        // Add indexes to order_items table (only if they don't exist)
        Schema::table('order_items', function (Blueprint $table) {
            $existingIndexes = $this->getTableIndexes('order_items');

            // These indexes might already exist from the original migration
            // Only create if they don't exist
            if (!in_array('order_id', $existingIndexes)) {
                $table->index('order_id', 'idx_order_items_order_perf');
            }

            if (!in_array('product_id', $existingIndexes)) {
                $table->index('product_id', 'idx_order_items_product_perf');
            }

            // Add index on status if it doesn't exist
            if (!in_array('status', $existingIndexes)) {
                $table->index('status', 'idx_order_items_status_perf');
            }
        });

        // Add indexes to products table
        Schema::table('products', function (Blueprint $table) {
            $existingIndexes = $this->getTableIndexes('products');

            // Index for active products (might already exist)
            if (!in_array('is_active', $existingIndexes)) {
                $table->index('is_active', 'idx_products_is_active_perf');
            }

            // Index for stock queries
            if (!in_array('stock_quantity', $existingIndexes)) {
                $table->index('stock_quantity', 'idx_products_stock_quantity');
            }

            // Category index might already exist
            if (!in_array('category_id', $existingIndexes)) {
                $table->index('category_id', 'idx_products_category_perf');
            }
        });

        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            $existingIndexes = $this->getTableIndexes('users');

            // Index for role filtering
            if (!in_array('role', $existingIndexes)) {
                $table->index('role', 'idx_users_role');
            }

            // Index for created_at for new user queries
            if (!in_array('created_at', $existingIndexes)) {
                $table->index('created_at', 'idx_users_created_at');
            }

            // Composite index for role and created_at
            if (!$this->compositeIndexExists('users', ['role', 'created_at'])) {
                $table->index(['role', 'created_at'], 'idx_users_role_created');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from orders table (only ones we created)
        Schema::table('orders', function (Blueprint $table) {
            $this->dropIndexIfExists('orders', 'idx_orders_created_at');
            $this->dropIndexIfExists('orders', 'idx_orders_payment_status_perf');
            $this->dropIndexIfExists('orders', 'idx_orders_status_perf');
            $this->dropIndexIfExists('orders', 'idx_orders_payment_created');
        });

        // Remove indexes from order_items table
        Schema::table('order_items', function (Blueprint $table) {
            $this->dropIndexIfExists('order_items', 'idx_order_items_order_perf');
            $this->dropIndexIfExists('order_items', 'idx_order_items_product_perf');
            $this->dropIndexIfExists('order_items', 'idx_order_items_status_perf');
        });

        // Remove indexes from products table
        Schema::table('products', function (Blueprint $table) {
            $this->dropIndexIfExists('products', 'idx_products_is_active_perf');
            $this->dropIndexIfExists('products', 'idx_products_stock_quantity');
            $this->dropIndexIfExists('products', 'idx_products_category_perf');
        });

        // Remove indexes from users table
        Schema::table('users', function (Blueprint $table) {
            $this->dropIndexIfExists('users', 'idx_users_role');
            $this->dropIndexIfExists('users', 'idx_users_created_at');
            $this->dropIndexIfExists('users', 'idx_users_role_created');
        });
    }

    /**
     * Get list of column names that have indexes on a table
     */
    private function getTableIndexes($tableName)
    {
        if (!Schema::hasTable($tableName)) {
            return [];
        }

        try {
            $indexes = DB::select("SHOW INDEX FROM {$tableName}");
            return collect($indexes)->pluck('Column_name')->unique()->toArray();
        } catch (\Exception $e) {
            // If there's an error, return empty array to be safe
            return [];
        }
    }

    /**
     * Check if a composite index exists on specified columns
     */
    private function compositeIndexExists($tableName, array $columns)
    {
        if (!Schema::hasTable($tableName)) {
            return false;
        }

        try {
            $indexes = DB::select("SHOW INDEX FROM {$tableName}");
            $groupedIndexes = collect($indexes)->groupBy('Key_name');

            foreach ($groupedIndexes as $indexName => $indexColumns) {
                $indexColumnNames = $indexColumns->pluck('Column_name')->toArray();
                if ($indexColumnNames === $columns) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Drop an index if it exists
     */
    private function dropIndexIfExists($tableName, $indexName)
    {
        try {
            if (Schema::hasTable($tableName)) {
                $indexes = DB::select("SHOW INDEX FROM {$tableName} WHERE Key_name = ?", [$indexName]);
                if (!empty($indexes)) {
                    DB::statement("DROP INDEX {$indexName} ON {$tableName}");
                }
            }
        } catch (\Exception $e) {
            // Index doesn't exist or error occurred, safe to ignore
        }
    }
};
