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
            $table->foreignId('cost_setting_id')->nullable()->after('product_id')->constrained('cost_settings')->nullOnDelete();
            
            // Profit type columns: 'percentage' or 'fixed'
            $table->string('dealer_profit_type', 20)->default('percentage')->after('dealer_profit_percent');
            $table->string('special_dealer_profit_type', 20)->default('percentage')->after('special_dealer_profit_percent');
            $table->string('normal_customer_profit_type', 20)->default('percentage')->after('normal_customer_profit_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_recipes', function (Blueprint $table) {
            $table->dropForeign(['cost_setting_id']);
            $table->dropColumn([
                'cost_setting_id',
                'dealer_profit_type',
                'special_dealer_profit_type',
                'normal_customer_profit_type',
            ]);
        });
    }
};
