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
        Schema::table('users', function (Blueprint $table) {
            $table->string('firstName'); // Maps to firstName
            $table->string('lastName'); // Maps to lastName
            $table->integer('age'); // Maps to age
            $table->string('gender'); // Maps to gender
            $table->string('phoneNo'); // Maps to phoneNo
            $table->string('address1'); // Maps to address1
            $table->string('address2')->nullable(); // Maps to address2, nullable
            $table->string('postcode'); // Maps to postcode
            $table->string('city'); // Maps to city
            $table->string('state'); // Maps to state
            $table->string('username')->unique(); // Maps to username
            $table->string('category'); // Maps to category
    });

    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
