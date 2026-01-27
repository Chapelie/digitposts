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
        Schema::create('registrations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('feed_id');
            $table->string('feed_type'); // App\Models\Training ou App\Models\Event
            $table->string('status')->default('pending');
            $table->string('payment_status')->default('pending');
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Contraintes de clé étrangère
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('feed_id')->references('id')->on('feeds')->onDelete('cascade');

            // Index composite pour la relation polymorphique
            $table->index(['feed_id', 'feed_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
