<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->string('event', 50)->index(); // 'created', 'updated', 'deleted', 'status_change', 'login', 'logout', etc.
            $table->string('action_type', 50)->nullable()->index(); // 'CREATE', 'UPDATE', 'DELETE', 'STATUS_CHANGE', 'AUTH', etc.
            $table->text('description')->nullable();
            $table->text('eventDetails')->nullable(); // SAL compatibility
            
            // Subject / Target Model
            $table->string('subject_type', 191)->nullable()->index();
            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->string('subject_ref', 100)->nullable()->index(); // e.g. "INV-1002" or product name
            
            // Actor / User
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_name')->nullable();
            $table->string('user_role', 50)->default('admin')->index(); // 'admin', 'worker', etc.
            
            // Temporal
            $table->date('date')->index();
            $table->time('time')->nullable();
            $table->dateTime('date_time')->nullable()->index();
            
            // Change Payload & Context
            $table->json('properties')->nullable();
            $table->string('from', 50)->default('web');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('route_name', 191)->nullable();
            $table->string('url', 255)->nullable();
            $table->string('method', 10)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_activities');
    }
}
