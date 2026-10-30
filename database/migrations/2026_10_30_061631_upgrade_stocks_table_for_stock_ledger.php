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
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE stocks MODIFY stock_type VARCHAR(50) NULL");

        Schema::table('stocks', function (Blueprint $table) {
            if (!Schema::hasColumn('stocks', 'before_stock')) {
                $table->decimal('before_stock', 12, 2)->default(0)->after('quantity');
            }
            if (!Schema::hasColumn('stocks', 'after_stock')) {
                $table->decimal('after_stock', 12, 2)->default(0)->after('before_stock');
            }
            if (!Schema::hasColumn('stocks', 'source_type')) {
                $table->string('source_type', 50)->nullable()->after('stock_type');
            }
            if (!Schema::hasColumn('stocks', 'source_id')) {
                $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
            }
            if (!Schema::hasColumn('stocks', 'reference_no')) {
                $table->string('reference_no', 100)->nullable()->after('source_id');
            }
            if (!Schema::hasColumn('stocks', 'note')) {
                $table->text('note')->nullable()->after('reference_no');
            }
            if (!Schema::hasColumn('stocks', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('note');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $cols = ['before_stock', 'after_stock', 'source_type', 'source_id', 'reference_no', 'note', 'created_by'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('stocks', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
