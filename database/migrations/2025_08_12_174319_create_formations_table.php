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
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->json('teachers'); // Store teachers as JSON array
            $table->string('duration')->nullable(); // Optional field
            $table->string('level')->nullable(); // Optional field
            $table->string('price');
            $table->enum('status', ['Active', 'Coming Soon', 'Completed'])->default('Active');
            $table->integer('enrolled_students')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};