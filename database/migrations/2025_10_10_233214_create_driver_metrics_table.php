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
        Schema::create('driver_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id');
            $table->date('record_date');
            $table->decimal('miles_driven', 10, 2)->nullable();
            $table->decimal('fuel_consumed', 10, 2)->nullable();
            $table->integer('deliveries_completed')->nullable();
            $table->decimal('on_time_percentage', 5, 2)->nullable();
            $table->integer('hard_brakes')->nullable();
            $table->integer('rapid_accelerations')->nullable();
            $table->integer('speeding_incidents')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamps();

            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['driver_id', 'record_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_metrics');
    }
};
