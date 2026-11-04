<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $tables = [
        'categories',
        'sub_categories',
        'units',
        'product_attributes',
        'variations',
        'variation_values',
        'material_types',
        'cost_heads',
        'banners',
        'coupons',
        'contact_messages',
        'subscribers',
        'products',
        'product_images',
        'product_attribute_values',
        'product_variation',
        'product_recipes',
        'product_recipe_items',
        'raw_material_products',
        'cost_settings',
        'cost_setting_items',
        'fabrics',
        'fabric_prices',
        'customers',
        'customer_addresses',
        'dealers',
        'vendors',
        'workers',
        'custom_orders',
        'custom_order_items',
        'custom_order_images',
        'pos_orders',
        'pos_order_items',
        'dealer_orders',
        'dealer_order_items',
        'dealer_order_payments',
        'draft_orders',
        'draft_order_items',
        'manufactures',
        'manufacture_order_items',
        'purchases',
        'purchase_items',
        'inventory_purchases',
        'inventory_purchase_items',
        'inventory_purchase_payments',
        'stock_adjustments',
        'worker_payments',
        'worker_payouts',
        'worker_payout_items',
        'wishlists',
        'product_reviews',
        'vendor_payments',
        'customer_payments',
        'order_requests',
        'order_request_items',
        'web_orders',
        'web_order_items',
        'web_order_addresses',
        'collections',
        'returns',
        'return_items',
    ];

    protected array $existingSoftDeleteTables = [
        'admins',
        'sellers',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (! Schema::hasColumn($tableName, 'deleted_at')) {
                    $table->softDeletes();
                }
                if (! Schema::hasColumn($tableName, 'deleted_by')) {
                    $table->unsignedBigInteger('deleted_by')->nullable()->after('deleted_at');
                }
                if (! Schema::hasColumn($tableName, 'deletion_batch_id')) {
                    $table->uuid('deletion_batch_id')->nullable()->index()->after('deleted_by');
                }
            });
        }

        foreach ($this->existingSoftDeleteTables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (! Schema::hasColumn($tableName, 'deleted_by')) {
                    $table->unsignedBigInteger('deleted_by')->nullable()->after('deleted_at');
                }
                if (! Schema::hasColumn($tableName, 'deletion_batch_id')) {
                    $table->uuid('deletion_batch_id')->nullable()->index()->after('deleted_by');
                }
            });
        }
    }

    public function down(): void
    {
        foreach (array_merge($this->tables, $this->existingSoftDeleteTables) as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'deletion_batch_id')) {
                    $table->dropColumn('deletion_batch_id');
                }
                if (Schema::hasColumn($tableName, 'deleted_by')) {
                    $table->dropColumn('deleted_by');
                }
                if (in_array($tableName, $this->tables, true) && Schema::hasColumn($tableName, 'deleted_at')) {
                    $table->dropSoftDeletes();
                }
            });
        }
    }
};
