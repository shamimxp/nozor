<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->boolean('is_received')->default(false)->after('reason');
            $table->timestamp('received_at')->nullable()->after('is_received');
            $table->unsignedBigInteger('received_by')->nullable()->after('received_at');
        });
    }

    public function down(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropColumn(['is_received', 'received_at', 'received_by']);
        });
    }
};
