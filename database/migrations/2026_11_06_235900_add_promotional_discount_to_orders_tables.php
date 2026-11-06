<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pos_orders') && ! Schema::hasColumn('pos_orders', 'promotional_discount')) {
            Schema::table('pos_orders', function (Blueprint $table) {
                $table->decimal('promotional_discount', 12, 2)->default(0)->after('discount_amount');
            });
        }

        if (Schema::hasTable('dealer_orders') && ! Schema::hasColumn('dealer_orders', 'promotional_discount')) {
            Schema::table('dealer_orders', function (Blueprint $table) {
                $table->decimal('promotional_discount', 12, 2)->default(0)->after('discount');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pos_orders') && Schema::hasColumn('pos_orders', 'promotional_discount')) {
            Schema::table('pos_orders', function (Blueprint $table) {
                $table->dropColumn('promotional_discount');
            });
        }

        if (Schema::hasTable('dealer_orders') && Schema::hasColumn('dealer_orders', 'promotional_discount')) {
            Schema::table('dealer_orders', function (Blueprint $table) {
                $table->dropColumn('promotional_discount');
            });
        }
    }
};
