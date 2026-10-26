<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add paid_amount and due_amount to worker_payments
        if (Schema::hasTable('worker_payments')) {
            Schema::table('worker_payments', function (Blueprint $table) {
                if (!Schema::hasColumn('worker_payments', 'paid_amount')) {
                    $table->decimal('paid_amount', 12, 2)->default(0)->after('total_amount');
                }
                if (!Schema::hasColumn('worker_payments', 'due_amount')) {
                    $table->decimal('due_amount', 12, 2)->default(0)->after('paid_amount');
                }
            });

            // Populate existing rows
            DB::statement("UPDATE worker_payments SET paid_amount = total_amount, due_amount = 0 WHERE status = 'paid'");
            DB::statement("UPDATE worker_payments SET paid_amount = 0, due_amount = total_amount WHERE status != 'paid' OR status IS NULL");
        }

        // 2. Create worker_payouts table
        if (!Schema::hasTable('worker_payouts')) {
            Schema::create('worker_payouts', function (Blueprint $table) {
                $table->id();
                $table->string('payment_number')->unique();
                $table->unsignedBigInteger('worker_id');
                $table->date('payment_date');
                $table->decimal('total_balance', 12, 2)->default(0);
                $table->decimal('amount', 12, 2);
                $table->decimal('remaining_balance', 12, 2)->default(0);
                $table->string('payment_method'); // Cash, Bank, bKash, Nagad, Rocket
                $table->string('account_details')->nullable(); // Trx ID / Account No / Phone
                $table->text('note')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade');
            });
        }

        // 3. Create worker_payout_items table (tracks allocation of payout to specific invoices)
        if (!Schema::hasTable('worker_payout_items')) {
            Schema::create('worker_payout_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('worker_payout_id');
                $table->unsignedBigInteger('worker_payment_id');
                $table->decimal('amount', 12, 2);
                $table->timestamps();

                $table->foreign('worker_payout_id')->references('id')->on('worker_payouts')->onDelete('cascade');
                $table->foreign('worker_payment_id')->references('id')->on('worker_payments')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_payout_items');
        Schema::dropIfExists('worker_payouts');

        if (Schema::hasTable('worker_payments')) {
            Schema::table('worker_payments', function (Blueprint $table) {
                if (Schema::hasColumn('worker_payments', 'due_amount')) {
                    $table->dropColumn('due_amount');
                }
                if (Schema::hasColumn('worker_payments', 'paid_amount')) {
                    $table->dropColumn('paid_amount');
                }
            });
        }
    }
};
