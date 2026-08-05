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
        Schema::table('dealer_order_items', function (Blueprint $table) {
            $table->integer('confirm_qty')->nullable()->after('qty');
            $table->text('note')->nullable()->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dealer_order_items', function (Blueprint $table) {
            $table->dropColumn(['confirm_qty', 'note']);
        });
    }
};
