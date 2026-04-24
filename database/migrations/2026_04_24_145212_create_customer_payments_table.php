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
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->nullableMorphs('payable'); // payable_id, payable_type (CustomOrder, PosOrder)
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->default('Cash');
            $table->string('payment_for')->nullable(); // web order, custom order, pos_order
            $table->date('payment_date');
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_payments');
    }
};
