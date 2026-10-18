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
            $table->unsignedBigInteger('product_id')->nullable()->change();
        });

        Schema::create('manufacture_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manufacture_id');
            $table->unsignedBigInteger('product_id');
            $table->string('reff_invoice')->nullable();
            $table->unsignedBigInteger('cost_setting_id')->nullable();
            $table->string('cost_title')->nullable();
            $table->string('part_type', 50)->default('body');
            $table->decimal('body_part_price', 15, 2)->default(0);
            $table->decimal('finishing_part_price', 15, 2)->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->integer('qty')->default(1);
            $table->decimal('total_price', 15, 2)->default(0);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('manufacture_id')->references('id')->on('manufactures')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        // Backfill existing records
        $existing = \DB::table('manufactures')->get();
        foreach ($existing as $m) {
            $partType = 'body';
            $unitPrice = $m->body_part_price;
            if ($m->body_total > 0 && $m->finishing_total > 0) {
                $partType = 'both';
                $unitPrice = $m->body_part_price + $m->finishing_part_price;
            } elseif ($m->finishing_total > 0) {
                $partType = 'finishing';
                $unitPrice = $m->finishing_part_price;
            }

            \DB::table('manufacture_order_items')->insert([
                'manufacture_id' => $m->id,
                'product_id' => $m->product_id,
                'reff_invoice' => $m->reff_invoice,
                'cost_setting_id' => null,
                'cost_title' => null,
                'part_type' => $partType,
                'body_part_price' => $m->body_part_price,
                'finishing_part_price' => $m->finishing_part_price,
                'unit_price' => $unitPrice,
                'qty' => $m->manufacture_qty,
                'total_price' => $m->grand_total,
                'note' => $m->note,
                'created_at' => $m->created_at ?? now(),
                'updated_at' => $m->updated_at ?? now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacture_order_items');
    }
};
