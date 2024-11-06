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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->increments('feedbackId'); // Primary key for the feedbacks table 
            $table->unsignedInteger('orderItemId'); // Foreign key or other column
            $table->integer('rating'); // Rating given
            $table->text('comments'); // Comments about the feedback
            $table->unsignedBigInteger('id'); // Foreign key or other column
            $table->timestamp('commentsTime')->nullable(); // Timestamp of when comments were made
            $table->text('staffResponse')->nullable(); // Response from staff (optional)
            $table->timestamp('responseTimestamp')->nullable(); // Timestamp of staff response (optional)
            $table->string('anonymous'); // Indicates whether the feedback is anonymous

            $table->timestamps(); // Laravel's created_at and updated_at columns

            // Define foreign key constraints if needed
            $table->foreign('orderItemId')->references('orderItemId')->on('orderitems')->onDelete('cascade');
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
