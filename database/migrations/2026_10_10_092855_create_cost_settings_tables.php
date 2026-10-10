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
        Schema::create('cost_settings', function (Blueprint $table) {
            $table->id();
            $table->string('cost_title')->unique();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('cost_setting_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_setting_id')->constrained('cost_settings')->onDelete('cascade');
            $table->foreignId('cost_head_id')->constrained('cost_heads')->onDelete('cascade');
            $table->decimal('cost_price', 12, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['cost_setting_id', 'cost_head_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_setting_items');
        Schema::dropIfExists('cost_settings');
    }
};
