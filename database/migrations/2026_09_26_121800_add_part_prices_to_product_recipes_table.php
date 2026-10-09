<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_recipes', function (Blueprint $table) {
            $table->decimal('body_part_price', 10, 2)->nullable()->after('product_id');
            $table->decimal('finishing_part_price', 10, 2)->nullable()->after('body_part_price');
        });

        $setting = DB::table('web_settings')->first();
        if ($setting) {
            DB::table('product_recipes')->update([
                'body_part_price' => $setting->body_part_price,
                'finishing_part_price' => $setting->finishing_part_price,
            ]);
        }

        DB::statement('ALTER TABLE products MODIFY selling_price DECIMAL(15,2) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_recipes', function (Blueprint $table) {
            $table->dropColumn(['body_part_price', 'finishing_part_price']);
        });

        DB::statement('ALTER TABLE products MODIFY selling_price DECIMAL(15,2) NOT NULL DEFAULT 0');
    }
};
