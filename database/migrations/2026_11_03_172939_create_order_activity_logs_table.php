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
        Schema::create('order_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('order_type', 50)->index(); // pos_order, web_order, dealer_order, order_request
            $table->unsignedBigInteger('order_id')->index();
            $table->string('order_number', 100)->nullable()->index();
            $table->string('action', 50)->index(); // created, status_change, updated, item_updated, payment, approved, rejected, delivered, cancelled
            $table->text('description');
            $table->string('from_status', 50)->nullable();
            $table->string('to_status', 50)->nullable();
            
            // Actor Details
            $table->unsignedBigInteger('actor_id')->nullable()->index();
            $table->string('actor_name', 150);
            $table->string('actor_type', 50)->default('system')->index(); // admin, worker, dealer, customer, system
            
            // Context & Audit trail
            $table->text('remarks')->nullable();
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();

            $table->index(['order_type', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_activity_logs');
    }
};
