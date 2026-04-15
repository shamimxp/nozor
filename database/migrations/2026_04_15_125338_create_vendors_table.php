<?php
/**
 * Vendor Migration
 *
 * This migration creates the vendors table with the following fields:
 * - name: The vendor's name
 * - company_name: The company name of the vendor
 * - phone: The vendor's phone number
 * - email: The vendor's email address
 * - opening_balance: The default opening balance (default: 0)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('name');
            $blueprint->string('company_name')->nullable();
            $blueprint->string('phone')->unique();
            $blueprint->string('email')->nullable()->unique();
            $blueprint->decimal('opening_balance', 15, 2)->default(0);
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendors');
    }
}
