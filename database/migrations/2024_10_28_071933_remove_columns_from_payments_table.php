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
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['receiptImage', 'responseDate', 'comments', 'staffFirstName', 'staffLastName']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->binary('receiptImage')->nullable();
            $table->timestamp('responseDate')->nullable();
            $table->string('comments')->nullable();
            $table->string('staffFirstName')->nullable();
            $table->string('staffLastName')->nullable();
        });
    }
};
