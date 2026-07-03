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
        Schema::table('raw_material_products', function (Blueprint $table) {
            $table->foreignId('material_type_id')->nullable()->constrained('material_types')->nullOnDelete()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raw_material_products', function (Blueprint $table) {
            $table->dropForeign(['material_type_id']);
            $table->dropColumn('material_type_id');
        });
    }
};
