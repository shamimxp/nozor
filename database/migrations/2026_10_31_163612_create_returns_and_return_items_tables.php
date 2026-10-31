<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_no', 30)->unique()->index();
            $table->date('return_date')->index();
            $table->string('channel', 30)->index(); // 'dealer', 'pos', 'web'
            
            // Party details (Polymorphic: Dealer, Customer, User)
            $table->string('party_type', 191)->nullable()->index();
            $table->unsignedBigInteger('party_id')->nullable()->index();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone', 50)->nullable();
            
            // Metrics and Financials
            $table->unsignedInteger('total_items_count')->default(0);
            $table->decimal('total_qty', 12, 2)->default(0);
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('deduction_amount', 14, 2)->default(0);
            $table->decimal('refund_amount', 14, 2)->default(0);
            
            // Refund method and Approval status
            $table->string('refund_type', 20)->default('cash')->index(); // 'cash', 'wallet'
            $table->string('status', 20)->default('pending')->index(); // 'pending', 'approved', 'rejected', 'cancelled'
            
            // Notes
            $table->text('return_note')->nullable(); // Master note added during creation
            $table->text('admin_review_note')->nullable(); // Note added by admin during review
            $table->text('reject_reason')->nullable();
            
            // Approval tracking
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            
            // Integrations
            $table->unsignedBigInteger('dealer_wallet_transaction_id')->nullable()->index();
            $table->boolean('stock_restocked')->default(false);
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained('returns')->onDelete('cascade');
            
            // Source Order Link
            $table->string('order_type', 30)->index(); // 'dealer', 'pos', 'web'
            $table->unsignedBigInteger('order_id')->index();
            $table->string('order_number', 100)->index();
            $table->unsignedBigInteger('order_item_id')->nullable();
            
            // Product info
            $table->foreignId('product_id')->constrained('products');
            $table->string('product_name');
            $table->string('product_code', 50)->nullable();
            
            // Quantities & Pricing
            $table->decimal('ordered_qty', 12, 2)->default(0);
            $table->decimal('previously_returned_qty', 12, 2)->default(0);
            $table->decimal('return_qty', 12, 2)->default(0);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('subtotal', 14, 2)->default(0);
            
            // Condition & Notes
            $table->string('condition', 30)->default('good'); // 'good', 'damaged', 'expired'
            $table->boolean('restock')->default(true);
            $table->string('item_note', 255)->nullable(); // Per-item reason or note
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('return_items');
        Schema::dropIfExists('returns');
    }
};
