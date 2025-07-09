<?php

// database/migrations/2025_04_24_000001_create_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relación con el comprador (User)
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Relación con el producto
            $table->enum('status', ['pending', 'processed', 'completed', 'cancelled'])->default('pending'); // Estado del pedido
            $table->timestamps();
        });

        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','processed','completed','cancelled') DEFAULT 'pending'");
    }

    public function down()
    {
        Schema::dropIfExists('orders');

        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','processed','completed') DEFAULT 'pending'");
    }
}
