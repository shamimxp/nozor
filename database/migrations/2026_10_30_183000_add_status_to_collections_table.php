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
        Schema::table('collections', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('payment_method')->index();
            $table->timestamp('accepted_at')->nullable()->after('status');
            $table->unsignedBigInteger('accepted_by')->nullable()->after('accepted_at');
            $table->timestamp('rejected_at')->nullable()->after('accepted_by');
            $table->unsignedBigInteger('rejected_by')->nullable()->after('rejected_at');
            $table->string('reject_reason', 500)->nullable()->after('rejected_by');
        });

        // Set existing records to 'accepted'
        DB::table('collections')->update([
            'status'      => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'accepted_at',
                'accepted_by',
                'rejected_at',
                'rejected_by',
                'reject_reason',
            ]);
        });
    }
};
