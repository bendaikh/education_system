<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('formation_payments')) {
            $hasForeignKeys = collect(DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'formation_payments'
                  AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            "))->isNotEmpty();

            if ($hasForeignKeys) {
                return;
            }

            Schema::dropIfExists('formation_payments');
        }

        Schema::create('formation_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('formation_subscriptions')->cascadeOnDelete();
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->string('status')->default('draft');
            $table->date('payment_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_payments');
    }
};
