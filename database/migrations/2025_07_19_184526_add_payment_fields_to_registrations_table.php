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
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('payment_transaction_id')->nullable()->after('payment_method');
            $table->text('payment_url')->nullable()->after('payment_transaction_id');
            $table->timestamp('payment_date')->nullable()->after('payment_url');
            $table->json('payment_details')->nullable()->after('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['payment_transaction_id', 'payment_url', 'payment_date', 'payment_details']);
        });
    }
};
