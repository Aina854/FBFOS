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
    Schema::table('orders', function (Blueprint $table) {
        $table->string('staffFirstName')->nullable()->change();
        $table->string('staffLastName')->nullable()->change();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {
        // Revert the staffFirstName and staffLastName columns to not nullable
        $table->string('staffFirstName')->nullable(false)->change();
        $table->string('staffLastName')->nullable(false)->change();
    });
}
};
