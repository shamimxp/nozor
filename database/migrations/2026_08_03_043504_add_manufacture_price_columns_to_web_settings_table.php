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
        Schema::table('web_settings', function (Blueprint $table) {
            $table->decimal('body_part_price', 10, 2)->nullable();
            $table->decimal('finishing_part_price', 10, 2)->nullable();
            $table->decimal('dealer_profit_percent', 5, 2)->nullable();
            $table->decimal('special_dealer_profit_percent', 5, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_settings', function (Blueprint $table) {
            $table->dropColumn([
                'body_part_price',
                'finishing_part_price',
                'dealer_profit_percent',
                'special_dealer_profit_percent'
            ]);
        });
    }
};
