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
        Schema::table('manufactures', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->after('is_confirm')->comment('0=pending, 1=confirmed, 2=completed');
            $table->unsignedBigInteger('completed_by')->nullable()->after('status');
            $table->string('collected_by')->nullable()->after('completed_by');
        });
        
        \DB::statement('UPDATE manufactures SET status = 1 WHERE is_confirm = 1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manufactures', function (Blueprint $table) {
            $table->dropColumn(['status', 'completed_by', 'collected_by']);
        });
    }
};
