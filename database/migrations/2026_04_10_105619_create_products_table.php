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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('sub_category_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('unit')->nullable();
            $table->text('short_description')->nullable();
            $table->integer('max_order_qty')->default(0);
            $table->boolean('is_featured')->default(0);
            $table->boolean('status')->default(1);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->string('featured_image')->nullable();
            $table->integer('stock')->default(0);
            $table->string('discount_type')->nullable()->comment('percent, amount');
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
