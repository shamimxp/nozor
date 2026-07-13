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
        Schema::table('dealer_orders', function (Blueprint $table) {
            $table->timestamp('request_date')->nullable();
            if (!Schema::hasColumn('dealer_orders', 'creator_id')) {
                $table->unsignedBigInteger('creator_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dealer_orders', function (Blueprint $table) {
            //
        });
    }
};
