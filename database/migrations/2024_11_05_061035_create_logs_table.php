<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->increments('logId'); // Primary key for the feedbacks table 
            $table->unsignedBigInteger('user_id')->nullable(); // User ID who performed the action
            $table->string('action'); // Description of the action
            $table->text('details')->nullable(); // Additional details about the action
            $table->timestamps(); // Created at and updated at timestamps

            // Foreign key constraint (optional, if you want to relate it to users)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
}
