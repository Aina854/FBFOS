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
            $table->string('firstName')->nullable()->change();
            $table->string('lastName')->nullable()->change();
            $table->integer('age')->nullable()->change();
            $table->string('gender')->nullable()->change();
            $table->string('phoneNo')->nullable()->change();
            $table->string('address1')->nullable()->change();
            $table->string('address2')->nullable()->change();
            $table->string('postcode')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('state')->nullable()->change();
            $table->string('category')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('firstName')->nullable(false)->change();
            $table->string('lastName')->nullable(false)->change();
            $table->integer('age')->nullable(false)->change();
            $table->string('gender')->nullable(false)->change();
            $table->string('phoneNo')->nullable(false)->change();
            $table->string('address1')->nullable(false)->change();
            $table->string('address2')->nullable(false)->change();
            $table->string('postcode')->nullable(false)->change();
            $table->string('city')->nullable(false)->change();
            $table->string('state')->nullable(false)->change();
            $table->string('category')->nullable(false)->change();
        });
    }
};
