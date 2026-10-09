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
        Schema::table('products', function (Blueprint $table) {
            $table->string('model_no')->nullable()->after('name');
            $table->boolean('dealer_show')->default(1)->after('is_manufacturer');
            $table->boolean('customer_show')->default(1)->after('dealer_show');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['model_no', 'dealer_show', 'customer_show']);
        });
    }
};
