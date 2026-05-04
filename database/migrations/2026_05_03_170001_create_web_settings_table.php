<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_settings', function (Blueprint $table) {
            $table->id();
            $table->string('slogan')->nullable();
            $table->string('contact_number_1')->nullable();
            $table->string('contact_number_2')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('pinterest')->nullable();
            $table->string('office_hour')->nullable();
            $table->string('currency_symbol')->nullable()->default('৳');
            $table->timestamps();
        });

        // Seed a default row so the settings page always has data to edit
        \Illuminate\Support\Facades\DB::table('web_settings')->insert([
            'slogan' => null,
            'currency_symbol' => '৳',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('web_settings');
    }
};
