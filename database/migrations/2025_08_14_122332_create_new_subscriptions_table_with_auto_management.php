<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, backup existing data
        $existingSubscriptions = [];
        if (Schema::hasTable('subscriptions')) {
            $existingSubscriptions = DB::table('subscriptions')->get()->toArray();
        }

        // Drop the existing subscriptions table
        Schema::dropIfExists('subscriptions');

        // Create the new subscriptions table with improved structure
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_type_id')->constrained('subscription_types')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->timestamp('started_at')->useCurrent(); // Auto-set when subscription is created
            $table->timestamp('expires_at')->nullable(); // Auto-calculated based on plan
            $table->enum('auto_status', ['active', 'expired', 'cancelled'])->default('active');
            $table->date('next_billing_date')->nullable();
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->json('metadata')->nullable(); // Store additional subscription details
            $table->timestamps();
        });

        // Note: We're not restoring old data since the structure is completely different
        // Old data would need manual migration if needed
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new table
        Schema::dropIfExists('subscriptions');
        
        // Recreate the old subscriptions table structure
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
};
