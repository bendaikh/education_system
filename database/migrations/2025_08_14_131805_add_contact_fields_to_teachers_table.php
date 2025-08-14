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
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('department')->nullable()->after('role'); // Teacher's department
            $table->string('contact_phone')->nullable()->after('department'); // Contact phone
            $table->string('contact_email')->nullable()->after('contact_phone'); // Contact email
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['department', 'contact_phone', 'contact_email']);
        });
    }
};
