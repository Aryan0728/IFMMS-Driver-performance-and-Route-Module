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
        Schema::create('driver_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->date('period_start');
            $table->date('period_end')->nullable();
            $table->enum('period_type', ['daily', 'weekly', 'monthly', 'yearly']);
            $table->decimal('total_distance', 10, 2)->default(0);
            $table->integer('total_routes')->default(0);
            $table->decimal('average_fuel_efficiency', 8, 2)->nullable();
            $table->decimal('average_speed', 8, 2)->nullable();
            $table->decimal('on_time_percentage', 5, 2)->default(0);
            $table->decimal('safety_score', 5, 2)->default(0);
            $table->decimal('customer_rating', 3, 2)->nullable();
            $table->decimal('performance_score', 5, 2)->default(0);
            $table->timestamps();

            $table->index(['driver_id', 'period_type', 'period_start']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_performances');
    }
};
