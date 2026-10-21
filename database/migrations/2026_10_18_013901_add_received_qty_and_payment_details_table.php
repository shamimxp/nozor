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
        if (Schema::hasTable('manufactures') && !Schema::hasColumn('manufactures', 'received_qty')) {
            Schema::table('manufactures', function (Blueprint $table) {
                $table->unsignedInteger('received_qty')->default(0)->after('manufacture_qty');
            });
        }

        if (Schema::hasTable('manufacture_order_items') && !Schema::hasColumn('manufacture_order_items', 'received_qty')) {
            Schema::table('manufacture_order_items', function (Blueprint $table) {
                $table->unsignedInteger('received_qty')->default(0)->after('qty');
            });
        }

        if (Schema::hasTable('worker_payments')) {
            Schema::table('worker_payments', function (Blueprint $table) {
                if (!Schema::hasColumn('worker_payments', 'received_qty')) {
                    $table->unsignedInteger('received_qty')->nullable()->after('worker_id');
                }
                if (!Schema::hasColumn('worker_payments', 'unit_price')) {
                    $table->decimal('unit_price', 10, 2)->nullable()->after('received_qty');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('manufactures') && Schema::hasColumn('manufactures', 'received_qty')) {
            Schema::table('manufactures', function (Blueprint $table) {
                $table->dropColumn('received_qty');
            });
        }

        if (Schema::hasTable('manufacture_order_items') && Schema::hasColumn('manufacture_order_items', 'received_qty')) {
            Schema::table('manufacture_order_items', function (Blueprint $table) {
                $table->dropColumn('received_qty');
            });
        }

        if (Schema::hasTable('worker_payments')) {
            Schema::table('worker_payments', function (Blueprint $table) {
                if (Schema::hasColumn('worker_payments', 'received_qty')) {
                    $table->dropColumn('received_qty');
                }
                if (Schema::hasColumn('worker_payments', 'unit_price')) {
                    $table->dropColumn('unit_price');
                }
            });
        }
    }
};
