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
        Schema::create('edu_support_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('educational_support_subscriptions')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['draft', 'paid', 'cancelled'])->default('draft');
            $table->date('payment_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edu_support_payments');
    }
};
