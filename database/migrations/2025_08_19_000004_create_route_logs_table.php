<?php
// database/migrations/2025_08_19_000004_create_route_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('route_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('odometer', 10, 2);
            $table->decimal('speed', 6, 2)->nullable();
            $table->decimal('fuel_level', 5, 2)->nullable();
            $table->string('location_name')->nullable();
            $table->timestamp('recorded_at');
            
            $table->index(['route_id', 'recorded_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('route_logs');
    }
};