<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('draft_orders') && ! Schema::hasColumn('draft_orders', 'promotional_discount')) {
            Schema::table('draft_orders', function (Blueprint $table) {
                $table->decimal('promotional_discount', 15, 2)->default(0)->after('discount');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('draft_orders') && Schema::hasColumn('draft_orders', 'promotional_discount')) {
            Schema::table('draft_orders', function (Blueprint $table) {
                $table->dropColumn('promotional_discount');
            });
        }
    }
};
