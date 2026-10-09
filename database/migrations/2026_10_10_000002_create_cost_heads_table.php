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
        Schema::create('cost_heads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        // Insert initial cost heads
        $defaultCostHeads = [
            'Body Charge',
            'Finishing Charge',
            'Bearing',
            'Stone',
            'Electric Bill',
            'Gas Bill',
            'Box Price',
            'Carrying Charge',
            'Wire Price',
            'Pitol Price',
        ];

        $now = now();
        $records = array_map(function ($title) use ($now) {
            return [
                'title' => $title,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $defaultCostHeads);

        DB::table('cost_heads')->insert($records);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_heads');
    }
};
