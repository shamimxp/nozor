<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Central Collections Table
        if (!Schema::hasTable('collections')) {
            Schema::create('collections', function (Blueprint $table) {
                $table->id();
                $table->string('collection_no', 50)->unique();
                $table->string('collection_type', 50); // 'dealer', 'pos_order', 'web_order', 'custom_order'
                $table->string('party_type', 100)->nullable();
                $table->unsignedBigInteger('party_id')->nullable()->index();
                $table->string('source_type', 100)->nullable();
                $table->unsignedBigInteger('source_id')->nullable()->index();
                $table->decimal('amount', 15, 2);
                $table->string('payment_method', 50)->default('Cash'); // Cash, Bank, bKash, Nagad, Rocket
                $table->string('transaction_id', 191)->nullable();
                $table->string('bank_name', 191)->nullable();
                $table->string('account_no', 191)->nullable();
                $table->date('payment_date');
                $table->text('note')->nullable();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->timestamps();
            });
        }

        // 2. Add wallet_balance to dealers table
        if (Schema::hasTable('dealers') && !Schema::hasColumn('dealers', 'wallet_balance')) {
            Schema::table('dealers', function (Blueprint $table) {
                $table->decimal('wallet_balance', 15, 2)->default(0.00)->after('status');
            });
        }

        // 3. Dealer Wallet Transactions Ledger
        if (!Schema::hasTable('dealer_wallet_transactions')) {
            Schema::create('dealer_wallet_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('dealer_id')->index();
                $table->string('transaction_no', 50)->unique();
                $table->unsignedBigInteger('collection_id')->nullable()->index();
                $table->unsignedBigInteger('dealer_order_id')->nullable()->index();
                $table->enum('type', ['credit', 'debit']); // credit = deposit, debit = deduction for order
                $table->decimal('amount', 15, 2);
                $table->decimal('before_balance', 15, 2);
                $table->decimal('after_balance', 15, 2);
                $table->string('payment_method', 50)->default('Wallet');
                $table->string('reference_no', 191)->nullable();
                $table->text('note')->nullable();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->timestamps();

                $table->foreign('dealer_id')->references('id')->on('dealers')->onDelete('cascade');
            });
        }

        // 4. Dealer Order Payments Breakdown
        if (!Schema::hasTable('dealer_order_payments')) {
            Schema::create('dealer_order_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('dealer_order_id')->index();
                $table->unsignedBigInteger('dealer_wallet_transaction_id')->nullable()->index();
                $table->unsignedBigInteger('collection_id')->nullable()->index();
                $table->decimal('amount', 15, 2);
                $table->date('payment_date');
                $table->text('note')->nullable();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->timestamps();

                $table->foreign('dealer_order_id')->references('id')->on('dealer_orders')->onDelete('cascade');
            });
        }

        // 5. Migrate legacy customer_payments into collections if any exist
        if (Schema::hasTable('customer_payments') && DB::table('collections')->count() === 0) {
            $legacyPayments = DB::table('customer_payments')->get();
            foreach ($legacyPayments as $cp) {
                $type = 'customer';
                if ($cp->payment_for === 'pos_order') {
                    $type = 'pos_order';
                } elseif ($cp->payment_for === 'custom_order') {
                    $type = 'custom_order';
                }

                DB::table('collections')->insert([
                    'collection_no'   => 'COL-LEGACY-' . str_pad($cp->id, 5, '0', STR_PAD_LEFT),
                    'collection_type' => $type,
                    'party_type'      => 'App\Models\Customer',
                    'party_id'        => $cp->customer_id,
                    'source_type'     => $cp->payable_type,
                    'source_id'       => $cp->payable_id,
                    'amount'          => $cp->amount,
                    'payment_method'  => $cp->payment_method ?? 'Cash',
                    'transaction_id'  => null,
                    'bank_name'       => null,
                    'account_no'      => null,
                    'payment_date'    => $cp->payment_date,
                    'note'            => $cp->note,
                    'created_by'      => $cp->created_by,
                    'created_at'      => $cp->created_at ?? now(),
                    'updated_at'      => $cp->updated_at ?? now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dealer_order_payments');
        Schema::dropIfExists('dealer_wallet_transactions');

        if (Schema::hasTable('dealers') && Schema::hasColumn('dealers', 'wallet_balance')) {
            Schema::table('dealers', function (Blueprint $table) {
                $table->dropColumn('wallet_balance');
            });
        }

        Schema::dropIfExists('collections');
    }
};
