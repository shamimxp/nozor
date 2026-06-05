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
            $table->decimal('inside_dhaka', 10, 2)->default(50)->nullable();
            $table->decimal('outside_dhaka', 10, 2)->default(100)->nullable();
            $table->decimal('subcity', 10, 2)->default(70)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_settings', function (Blueprint $table) {
            $table->dropColumn(['inside_dhaka', 'outside_dhaka', 'subcity']);
        });
    }
};
