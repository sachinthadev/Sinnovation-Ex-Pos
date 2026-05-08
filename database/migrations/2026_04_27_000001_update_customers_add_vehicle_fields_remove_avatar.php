<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('vehicle_number')->nullable()->after('phone');
            $table->string('chassis_number')->nullable()->after('vehicle_number');
            $table->string('vehicle_model')->nullable()->after('chassis_number');
            $table->dropColumn('avatar');
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('address');
            $table->dropColumn(['vehicle_number', 'chassis_number', 'vehicle_model']);
        });
    }
};
