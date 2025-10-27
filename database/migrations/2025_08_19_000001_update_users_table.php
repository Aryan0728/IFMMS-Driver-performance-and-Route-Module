<?php
// database/migrations/2025_08_19_000001_update_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // First check if role column exists, if not add it
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['Admin', 'Driver', 'Technician'])->default('Driver')->after('email_verified_at');
            }
            
            // Add other columns
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
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Only drop foreign key if it exists
            if (Schema::hasColumn('users', 'vehicle_id')) {
                $table->dropForeign(['vehicle_id']);
            }
            
            // Drop columns if they exist
            $columnsToDrop = ['role', 'license_number', 'hired_date', 'vehicle_id'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};