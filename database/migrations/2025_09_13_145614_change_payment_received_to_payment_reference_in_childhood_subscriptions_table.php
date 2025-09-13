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
        Schema::table('childhood_subscriptions', function (Blueprint $table) {
            $table->dropColumn('number_of_payments_received');
            $table->string('payment_reference_number')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('childhood_subscriptions', function (Blueprint $table) {
            $table->dropColumn('payment_reference_number');
            $table->integer('number_of_payments_received')->default(0)->after('notes');
        });
    }
};
