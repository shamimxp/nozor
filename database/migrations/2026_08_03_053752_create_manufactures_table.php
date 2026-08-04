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
        Schema::create('manufactures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('invoice_no')->nullable();
            $table->unsignedBigInteger('dealer_id')->nullable();
            $table->string('dealer_name')->nullable();
            $table->string('dealer_phone')->nullable();
            $table->string('dealer_address')->nullable();
            $table->integer('manufacture_qty')->default(1);
            $table->decimal('body_part_price', 15, 2)->default(0);
            $table->decimal('finishing_part_price', 15, 2)->default(0);
            $table->decimal('body_total', 15, 2)->default(0);
            $table->decimal('finishing_total', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->text('note')->nullable();
            $table->boolean('is_confirm')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufactures');
    }
};
