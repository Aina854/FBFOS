<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePaymentsTableAllowNullStaffNames extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Set staffFirstName and staffLastName to nullable
            $table->string('staffFirstName')->nullable()->change();
            $table->string('staffLastName')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Revert back to not nullable if needed
            $table->string('staffFirstName')->nullable(false)->change();
            $table->string('staffLastName')->nullable(false)->change();
        });
    }
}
