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
        if (Schema::hasColumn('educational_subjects', 'duration')) {
            return;
        }

        Schema::table('educational_subjects', function (Blueprint $table) {
            $table->enum('duration', ['monthly', 'yearly'])->default('monthly')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('educational_subjects', 'duration')) {
            return;
        }

        Schema::table('educational_subjects', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
    }
};
