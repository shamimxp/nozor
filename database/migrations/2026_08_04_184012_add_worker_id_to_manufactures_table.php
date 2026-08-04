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
        Schema::table('manufactures', function (Blueprint $table) {
            $table->unsignedBigInteger('worker_id')->nullable()->after('dealer_address');
             $table->string('reff_invoice')->nullable()->after('worker_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manufactures', function (Blueprint $table) {
            $table->dropColumn('worker_id');
            $table->dropColumn('reff_invoice');
        });
    }
};
