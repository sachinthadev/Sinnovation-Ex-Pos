<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {

            // Add vehicle_number if not exists
            if (!Schema::hasColumn('customers', 'vehicle_number')) {
                $table->string('vehicle_number')->nullable()->after('phone');
            }

            // Add chassis_number if not exists
            if (!Schema::hasColumn('customers', 'chassis_number')) {
                $table->string('chassis_number')->nullable()->after('vehicle_number');
            }

            // Add vehicle_model if not exists
            if (!Schema::hasColumn('customers', 'vehicle_model')) {
                $table->string('vehicle_model')->nullable()->after('chassis_number');
            }

            // Drop avatar only if exists
            if (Schema::hasColumn('customers', 'avatar')) {
                $table->dropColumn('avatar');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {

            // Recreate avatar if missing
            if (!Schema::hasColumn('customers', 'avatar')) {
                $table->string('avatar')->nullable()->after('address');
            }

            // Remove vehicle_number if exists
            if (Schema::hasColumn('customers', 'vehicle_number')) {
                $table->dropColumn('vehicle_number');
            }

            // Remove chassis_number if exists
            if (Schema::hasColumn('customers', 'chassis_number')) {
                $table->dropColumn('chassis_number');
            }

            // Remove vehicle_model if exists
            if (Schema::hasColumn('customers', 'vehicle_model')) {
                $table->dropColumn('vehicle_model');
            }
        });
    }
};