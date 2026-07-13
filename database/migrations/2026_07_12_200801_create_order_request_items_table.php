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
        Schema::create('order_request_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_request_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('requested_qty');
            $table->integer('confirmed_qty')->default(0);
            $table->timestamps();
            
            $table->foreign('order_request_id')->references('id')->on('order_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_request_items');
    }
};
