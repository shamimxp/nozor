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
        Schema::create('product_recipe_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_recipe_id')->constrained('product_recipes')->onDelete('cascade');
            $table->foreignId('raw_material_product_id')->constrained('raw_material_products')->onDelete('cascade');
            $table->decimal('thickness', 8, 2)->default(0);
            $table->decimal('width', 8, 2)->default(0);
            $table->decimal('height', 8, 2)->default(0);
            $table->decimal('area', 8, 2)->default(0);
            $table->decimal('grade_value', 8, 2)->default(0);
            $table->decimal('qty', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_recipe_items');
    }
};
