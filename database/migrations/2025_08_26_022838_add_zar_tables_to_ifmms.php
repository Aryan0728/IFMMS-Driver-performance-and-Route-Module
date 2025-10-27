<?php
// database/migrations/2025_08_26_000000_add_zar_tables_to_ifmms.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create vehicles table
        if (!Schema::hasTable('vehicles')) {
            Schema::create('vehicles', function (Blueprint $table) {
                $table->id();
                $table->string('make');
                $table->string('model');
                $table->year('year');
                $table->string('license_plate')->unique();
                $table->string('vin')->unique();
                $table->decimal('fuel_efficiency', 5, 2)->comment('miles per gallon');
                $table->integer('odometer');
                $table->date('last_maintenance_date');
                $table->enum('status', ['available', 'in_use', 'maintenance', 'out_of_service'])->default('available');
                $table->timestamps();
            });
        }

        // Update users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'license_number')) {
                $table->string('license_number')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'hired_date')) {
                $table->date('hired_date')->nullable()->after('license_number');
            }
            if (!Schema::hasColumn('users', 'vehicle_id')) {
                $table->foreignId('vehicle_id')->nullable()->after('hired_date');
            }
        });

        // Create driver_metrics table
        if (!Schema::hasTable('driver_metrics')) {
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

        // Create routes table
        if (!Schema::hasTable('routes')) {
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

        // Create route_logs table
        if (!Schema::hasTable('route_logs')) {
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
    }

    public function down()
    {
        // Don't drop tables in production, just remove the new columns
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['license_number', 'hired_date', 'vehicle_id']);
        });
    }
};