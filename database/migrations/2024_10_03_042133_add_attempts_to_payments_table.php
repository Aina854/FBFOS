<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->integer('attempts')->default(0); // Track payment attempts
            $table->timestamp('last_attempt_at')->nullable(); // Track the last attempt time
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('attempts'); // Remove the attempts column
            $table->dropColumn('last_attempt_at'); // Remove the last_attempt_at column
        });
    }
};
