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
        Schema::table('edu_support_payments', function (Blueprint $table) {
            $table->enum('status', ['draft', 'paid', 'partial', 'cancelled'])->default('draft')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('edu_support_payments', function (Blueprint $table) {
            $table->enum('status', ['draft', 'paid', 'cancelled'])->default('draft')->change();
        });
    }
};
