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
        Schema::create('orderitems', function (Blueprint $table) {
            $table->increments('orderItemId'); // Primary key
            $table->unsignedInteger('orderId'); // Foreign key to orders table
            $table->unsignedInteger('menuId'); // Foreign key to menu table
            $table->unsignedInteger('quantity'); // Quantity of the item
            $table->decimal('price', 10, 2); // Price of the item
            $table->decimal('totalPrice', 10, 2); // Total price (Quantity * Price)

            $table->timestamps(); // Laravel's created_at and updated_at columns

            // Define foreign key constraints if needed
            $table->foreign('orderId')->references('orderId')->on('orders')->onDelete('cascade');
            $table->foreign('menuId')->references('menuId')->on('menus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orderitems');
    }
};
