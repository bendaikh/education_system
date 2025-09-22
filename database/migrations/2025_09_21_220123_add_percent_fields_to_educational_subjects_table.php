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
        Schema::table('educational_subjects', function (Blueprint $table) {
            $table->decimal('school_percent', 5, 2)->default(50.00)->after('price');
            $table->decimal('teacher_percent', 5, 2)->default(50.00)->after('school_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('educational_subjects', function (Blueprint $table) {
            $table->dropColumn(['school_percent', 'teacher_percent']);
        });
    }
};
