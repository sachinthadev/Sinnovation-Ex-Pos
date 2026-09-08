<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart', function (Blueprint $table): void {
            $table->decimal('commission_percentage', 5, 2)->nullable()->after('employee_id');
        });

        Schema::table('order_items', function (Blueprint $table): void {
            $table->decimal('commission_percentage', 5, 2)->nullable()->after('employee_id');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->dropColumn('commission_percentage');
        });

        Schema::table('cart', function (Blueprint $table): void {
            $table->dropColumn('commission_percentage');
        });
    }
};