<?php
// database/migrations/2025_08_19_000003_create_routes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('assigned_driver_id')->constrained('users');
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->dateTime('start_time');
            $table->dateTime('estimated_end_time');
            $table->enum('status', ['planned', 'in_progress', 'completed', 'delayed', 'canceled'])->default('planned');
            $table->json('optimized_waypoints');
            $table->decimal('estimated_distance', 8, 2)->comment('in miles');
            $table->decimal('actual_distance', 8, 2)->nullable();
            $table->integer('estimated_duration')->comment('in minutes');
            $table->integer('actual_duration')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('routes');
    }
};