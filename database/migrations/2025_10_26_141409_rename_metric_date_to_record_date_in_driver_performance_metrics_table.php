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
        Schema::table('driver_performance_metrics', function (Blueprint $table) {
            $table->renameColumn('metric_date', 'record_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_performance_metrics', function (Blueprint $table) {
            $table->renameColumn('record_date', 'metric_date');
        });
    }
};
