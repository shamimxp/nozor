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
        Schema::table('product_recipes', function (Blueprint $table) {
            $table->decimal('pitol_price', 10, 2)->default(0)->after('wire_price');
            $table->decimal('dealer_profit_percent', 8, 2)->default(0)->after('carrying_charge');
            $table->decimal('special_dealer_profit_percent', 8, 2)->default(0)->after('dealer_profit_percent');
            $table->decimal('normal_customer_profit_percent', 8, 2)->default(0)->after('special_dealer_profit_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_recipes', function (Blueprint $table) {
            $table->dropColumn([
                'pitol_price',
                'dealer_profit_percent',
                'special_dealer_profit_percent',
                'normal_customer_profit_percent',
            ]);
        });
    }
};
