<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->date('from_date');
            $table->date('to_date');
            $table->unsignedInteger('from_qty')->default(1);
            $table->unsignedInteger('to_qty');
            $table->enum('offer_type', ['fixed', 'percent'])->default('fixed');
            $table->decimal('discount_value', 12, 2);
            $table->tinyInteger('status')->default(1)->comment('1:Active, 0:Inactive');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->uuid('deletion_batch_id')->nullable()->index();

            $table->index(['product_id', 'status']);
            $table->index(['from_date', 'to_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_offers');
    }
};
