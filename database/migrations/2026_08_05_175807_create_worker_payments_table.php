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
        Schema::create('worker_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manufacture_order_id')->nullable();
            $table->date('date')->nullable();
            $table->unsignedBigInteger('worker_id')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_payments');
    }
};
