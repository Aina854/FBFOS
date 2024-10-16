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
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('orderId'); // Primary key
            $table->unsignedBigInteger('id'); // Foreign key referencing the `users` table
            $table->string('OrderStatus'); // Status of the order
            $table->timestamp('OrderDate'); // Order date and time
            $table->string('remarks')->nullable(); // Optional remarks
            $table->string('staffFirstName'); // Staff first name
            $table->string('staffLastName'); // Staff last name
            $table->boolean('feedbackSubmitted')->default(0); // Feedback submitted flag

            $table->timestamps(); // Laravel's created_at and updated_at columns

            // Define foreign key constraint
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
