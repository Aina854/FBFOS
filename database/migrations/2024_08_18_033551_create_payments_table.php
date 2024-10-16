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
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('paymentId'); // Primary key for the payments table
            $table->unsignedBigInteger('id'); // Foreign key or other column
            $table->unsignedInteger('orderId'); // Foreign key or other column
            $table->decimal('paymentAmount', 10, 2); // Amount of the payment
            $table->timestamp('paymentDate'); // Date and time of payment
            $table->binary('receiptImage')->nullable();; // Receipt image (optional)
            $table->string('paymentStatus'); // Status of the payment
            $table->timestamp('responseDate')->nullable(); // Response date (optional)
            $table->string('comments')->nullable(); // Comments (optional)
            $table->string('paymentMethod'); // Method of payment
            $table->string('staffFirstName'); // Staff first name
            $table->string('staffLastName'); // Staff last name

            $table->timestamps(); // Laravel's created_at and updated_at columns

            // Define foreign key constraints if needed
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('orderId')->references('orderId')->on('orders')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
