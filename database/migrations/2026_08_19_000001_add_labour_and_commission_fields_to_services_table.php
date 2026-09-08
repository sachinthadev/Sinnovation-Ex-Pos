<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->decimal('commission_percentage', 5, 2)->nullable()->after('price');
            $table->string('labour_type')->nullable()->after('type');
            $table->string('allowed_service_type')->nullable()->after('labour_type');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->dropColumn([
                'commission_percentage',
                'labour_type',
                'allowed_service_type',
            ]);
        });
    }
};