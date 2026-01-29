<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add performance indexes for MLM commission calculations:
     * - User referral lookups (referred_by)
     * - Date range queries (enrolled_date, order_date)
     * - Foreign key relationships
     * - Composite indexes for common query patterns
     */
    public function up(): void
    {
        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            // Index for referral lookups (who referred this user)
            $table->index('referred_by', 'idx_users_referred_by');
            
            // Index for date range queries
            $table->index('enrolled_date', 'idx_users_enrolled_date');
            
            // Composite index for referral + date queries
            $table->index(['referred_by', 'enrolled_date'], 'idx_users_referral_date');
        });

        // Add indexes to orders table
        Schema::table('orders', function (Blueprint $table) {
            // Index for purchaser lookups
            $table->index('purchaser_id', 'idx_orders_purchaser_id');
            
            // Index for date range queries
            $table->index('order_date', 'idx_orders_order_date');
            
            // Composite index for purchaser + date queries
            $table->index(['purchaser_id', 'order_date'], 'idx_orders_purchaser_date');
            
            // Index for invoice lookups
            $table->index('invoice_number', 'idx_orders_invoice_number');
        });

        // Add indexes to order_items table
        Schema::table('order_items', function (Blueprint $table) {
            // Index for order lookups
            $table->index('order_id', 'idx_order_items_order_id');
            
            // Index for product lookups
            $table->index('product_id', 'idx_order_items_product_id');
        });

        // Add indexes to user_category table
        Schema::table('user_category', function (Blueprint $table) {
            // Index for user lookups
            $table->index('user_id', 'idx_user_category_user_id');
            
            // Index for category lookups
            $table->index('category_id', 'idx_user_category_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_referred_by');
            $table->dropIndex('idx_users_enrolled_date');
            $table->dropIndex('idx_users_referral_date');
        });

        // Drop indexes from orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_purchaser_id');
            $table->dropIndex('idx_orders_order_date');
            $table->dropIndex('idx_orders_purchaser_date');
            $table->dropIndex('idx_orders_invoice_number');
        });

        // Drop indexes from order_items table
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('idx_order_items_order_id');
            $table->dropIndex('idx_order_items_product_id');
        });

        // Drop indexes from user_category table
        Schema::table('user_category', function (Blueprint $table) {
            $table->dropIndex('idx_user_category_user_id');
            $table->dropIndex('idx_user_category_category_id');
        });
    }
};
