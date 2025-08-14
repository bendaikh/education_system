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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_type_id')->constrained('subscription_types')->onDelete('cascade');
            $table->string('subscriber_name'); // Organization/school name
            $table->string('subscriber_email');
            $table->string('subscriber_phone')->nullable();
            $table->enum('status', ['active', 'trial', 'expired', 'cancelled'])->default('trial');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('next_billing_date')->nullable();
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->json('metadata')->nullable(); // Store additional subscription details
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
