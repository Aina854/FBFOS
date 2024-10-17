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
        Schema::table('menus', function (Blueprint $table) {
            // Add the new quantityStock column
            $table->integer('quantityStock')->default(0); // Default to 0 if no stock data is provided

            // Remove the availability column
            $table->dropColumn('availability');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            // Re-add the availability column if needed
            $table->string('availability');

            // Remove the quantityStock column
            $table->dropColumn('quantityStock');
        });
    }
};
