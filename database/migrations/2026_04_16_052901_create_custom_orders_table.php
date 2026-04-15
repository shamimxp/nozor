<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_orders', function (Blueprint $table) {
            $table->id();
            $table->string('style_number')->unique();
            $table->date('order_date');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('type')->comment('polo, t-shirt');
            $table->string('sleeve')->comment('half, full');
            $table->text('customer_note')->nullable();
            $table->text('vendor_note')->nullable();
            $table->date('delivery_date')->nullable();
            $table->date('collected_date')->nullable();
            $table->integer('total_quantity')->default(0);
            $table->decimal('sub_total', 12, 2)->default(0.00);
            $table->decimal('carrying_charge', 12, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2)->default(0.00);
            $table->string('order_type')->default('take_away')->comment('take_away, home_delivery');
            $table->decimal('paid', 12, 2)->default(0.00);
            $table->decimal('due', 12, 2)->default(0.00);
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null');
            $table->string('status')->default('pending')->comment('pending, processing, completed, cancelled');
            $table->timestamps();
        });

        Schema::create('custom_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_order_id')->constrained('custom_orders')->onDelete('cascade');
            $table->foreignId('fabric_price_id')->constrained('fabric_prices')->onDelete('cascade');
            $table->string('fabric_name');
            $table->string('type');
            $table->string('sleeve');
            $table->decimal('unit_price', 12, 2)->default(0.00);
            $table->integer('quantity')->default(1);
            $table->decimal('total', 12, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('custom_order_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_order_id')->constrained('custom_orders')->onDelete('cascade');
            $table->string('image');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_order_images');
        Schema::dropIfExists('custom_order_items');
        Schema::dropIfExists('custom_orders');
    }
};
