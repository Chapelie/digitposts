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
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('start_date');
            $table->dateTime('end_date'); // start_date + 3 semaines
            $table->decimal('amount', 10, 2)->default(2000.00); // 2000 XOF
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'cancelled'])->default('pending');
            $table->string('payment_transaction_id')->nullable();
            $table->text('payment_url')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->json('payment_details')->nullable();
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->timestamps();
            
            // Index pour améliorer les performances
            $table->index('user_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index(['start_date', 'end_date']);
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
