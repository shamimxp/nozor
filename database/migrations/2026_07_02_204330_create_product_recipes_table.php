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
        Schema::create('product_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('body_charge', 10, 2)->default(0);
            $table->decimal('finishing_charge', 10, 2)->default(0);
            $table->decimal('bearing', 10, 2)->default(0);
            $table->decimal('stone', 10, 2)->default(0);
            $table->decimal('electric_bill', 10, 2)->default(0);
            $table->decimal('gas_bill', 10, 2)->default(0);
            $table->decimal('box_price', 10, 2)->default(0);
            $table->decimal('wire_price', 10, 2)->default(0);
            $table->decimal('carrying_charge', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_recipes');
    }
};
