<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE inventory_purchases SET status = 'confirm' WHERE status = 'confirmed'");
        DB::statement("ALTER TABLE inventory_purchases MODIFY status ENUM('pending', 'confirm', 'received') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("UPDATE inventory_purchases SET status = 'confirmed' WHERE status = 'confirm'");
        DB::statement("ALTER TABLE inventory_purchases MODIFY status ENUM('pending', 'confirmed') NOT NULL DEFAULT 'pending'");
    }
};
