<?php
// database/migrations/2025_08_19_000002_create_driver_metrics_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('driver_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('users');
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->date('record_date');
            $table->decimal('miles_driven', 8, 2);
            $table->decimal('fuel_consumed', 8, 2);
            $table->integer('deliveries_completed');
            $table->integer('on_time_percentage');
            $table->integer('hard_brakes')->default(0);
            $table->integer('rapid_accelerations')->default(0);
            $table->integer('speeding_incidents')->default(0);
            $table->decimal('score', 5, 2)->comment('Composite performance score 0-100');
            $table->timestamps();
            
            $table->index(['driver_id', 'record_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('driver_metrics');
    }
};