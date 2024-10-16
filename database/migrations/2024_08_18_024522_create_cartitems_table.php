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
        Schema::create('cartitems', function (Blueprint $table) {
            $table->increments('cartItemId'); // Primary key
            $table->unsignedInteger('menuId'); // Foreign key column
            $table->unsignedInteger('quantity'); // Quantity column
            $table->decimal('price', 10, 2); // Price column
            $table->decimal('totalPrice', 10, 2); // Total price column
            $table->date('createdAt'); // Created at column
            $table->unsignedInteger('cartId'); // Foreign key column
            $table->timestamps(); // Laravel's created_at and updated_at columns

            // Define foreign key constraints
            $table->foreign('menuId')->references('menuId')->on('menus')->onDelete('cascade');
            $table->foreign('cartId')->references('cartId')->on('carts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cartitems');
    }
};
