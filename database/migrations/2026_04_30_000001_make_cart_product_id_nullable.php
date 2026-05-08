<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cart')) {
            return;
        }

        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=off;');
            Schema::rename('cart', 'cart_old');

            Schema::create('cart', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->foreignId('product_id')->nullable();
                $table->string('name');
                $table->unsignedInteger('quantity');
                $table->decimal('price', 8, 2);
                $table->decimal('tax', 8, 2)->default('0.00');

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            });

            DB::statement(
                'INSERT INTO cart (id, user_id, product_id, name, quantity, price, tax) SELECT id, user_id, product_id, name, quantity, price, tax FROM cart_old;'
            );
            Schema::dropIfExists('cart_old');
            DB::statement('PRAGMA foreign_keys=on;');
        } else {
            Schema::table('cart', function (Blueprint $table) {
                $table->foreignId('product_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('cart')) {
            return;
        }

        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=off;');
            Schema::rename('cart', 'cart_old');

            Schema::create('cart', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->foreignId('product_id');
                $table->string('name');
                $table->unsignedInteger('quantity');
                $table->decimal('price', 8, 2);
                $table->decimal('tax', 8, 2)->default('0.00');

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            });

            DB::statement(
                'INSERT OR REPLACE INTO cart (id, user_id, product_id, name, quantity, price, tax) SELECT id, user_id, COALESCE(product_id, 0), name, quantity, price, tax FROM cart_old;'
            );
            Schema::dropIfExists('cart_old');
            DB::statement('PRAGMA foreign_keys=on;');
        } else {
            Schema::table('cart', function (Blueprint $table) {
                $table->foreignId('product_id')->nullable(false)->change();
            });
        }
    }
};
