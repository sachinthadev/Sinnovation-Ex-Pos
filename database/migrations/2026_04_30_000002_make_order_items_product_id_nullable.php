<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('order_items')) {
            return;
        }

        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=off;');
            Schema::rename('order_items', 'order_items_old');

            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->decimal('price', 8, 4);
                $table->integer('quantity')->default(1);
                $table->foreignId('order_id');
                $table->foreignId('product_id')->nullable();
                $table->string('name');
                $table->decimal('tax', 8, 2)->default('0.00');
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            });

            DB::statement(
                'INSERT INTO order_items (id, price, quantity, order_id, product_id, name, tax, created_at, updated_at) SELECT id, price, quantity, order_id, product_id, name, tax, created_at, updated_at FROM order_items_old;'
            );

            Schema::dropIfExists('order_items_old');
            DB::statement('PRAGMA foreign_keys=on;');
        } else {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreignId('product_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('order_items')) {
            return;
        }

        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=off;');
            Schema::rename('order_items', 'order_items_old');

            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->decimal('price', 8, 4);
                $table->integer('quantity')->default(1);
                $table->foreignId('order_id');
                $table->foreignId('product_id');
                $table->string('name');
                $table->decimal('tax', 8, 2)->default('0.00');
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            });

            DB::statement(
                'INSERT INTO order_items (id, price, quantity, order_id, product_id, name, tax, created_at, updated_at) SELECT id, price, quantity, order_id, COALESCE(product_id, 0), name, tax, created_at, updated_at FROM order_items_old;'
            );

            Schema::dropIfExists('order_items_old');
            DB::statement('PRAGMA foreign_keys=on;');
        } else {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreignId('product_id')->nullable(false)->change();
            });
        }
    }
};
