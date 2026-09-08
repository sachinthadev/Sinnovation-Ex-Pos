<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart', function (Blueprint $table): void {
            $table->foreignId('service_id')->nullable()->after('product_id')->constrained('services')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->after('service_id')->constrained('employees')->nullOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table): void {
            $table->foreignId('service_id')->nullable()->after('product_id')->constrained('services')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->after('service_id')->constrained('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['service_id']);
            $table->dropColumn(['employee_id', 'service_id']);
        });

        Schema::table('cart', function (Blueprint $table): void {
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['service_id']);
            $table->dropColumn(['employee_id', 'service_id']);
        });
    }
};