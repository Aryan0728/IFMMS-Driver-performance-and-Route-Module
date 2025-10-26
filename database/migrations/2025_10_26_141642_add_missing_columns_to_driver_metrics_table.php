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
        Schema::table('driver_metrics', function (Blueprint $table) {
            $table->decimal('total_distance', 10, 2)->nullable()->after('record_date');
            $table->decimal('total_driving_time', 8, 2)->nullable()->after('total_distance');
            $table->decimal('fuel_efficiency', 8, 2)->nullable()->after('total_driving_time');
            $table->decimal('average_speed', 8, 2)->nullable()->after('fuel_efficiency');
            $table->integer('routes_completed')->nullable()->after('average_speed');
            $table->integer('routes_assigned')->nullable()->after('routes_completed');
            $table->integer('safety_incidents')->nullable()->after('speeding_incidents');
            $table->integer('traffic_violations')->nullable()->after('safety_incidents');
            $table->decimal('customer_rating', 3, 2)->nullable()->after('traffic_violations');
            $table->decimal('overtime_hours', 8, 2)->nullable()->after('customer_rating');
            $table->decimal('idle_time', 8, 2)->nullable()->after('overtime_hours');
            $table->json('performance_scores')->nullable()->after('idle_time');
            $table->text('notes')->nullable()->after('performance_scores');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_metrics', function (Blueprint $table) {
            $table->dropColumn([
                'total_distance',
                'total_driving_time',
                'fuel_efficiency',
                'average_speed',
                'routes_completed',
                'routes_assigned',
                'customer_rating',
                'overtime_hours',
                'idle_time',
                'performance_scores',
                'notes'
            ]);
        });
    }
};
