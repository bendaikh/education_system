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
            $table->integer('number_of_payments_received')->default(0)->after('notes');
            $table->decimal('assurance_price', 10, 2)->nullable()->after('number_of_payments_received');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('childhood_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['number_of_payments_received', 'assurance_price']);
        });
    }
};
