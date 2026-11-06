<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_offers', function (Blueprint $table) {
            $table->unsignedInteger('from_qty')->default(1)->change();
            $table->unsignedInteger('to_qty')->change();
        });
    }

    public function down(): void
    {
        Schema::table('product_offers', function (Blueprint $table) {
            $table->decimal('from_qty', 12, 2)->default(1)->change();
            $table->decimal('to_qty', 12, 2)->change();
        });
    }
};
