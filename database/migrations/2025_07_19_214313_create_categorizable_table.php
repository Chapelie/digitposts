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
        Schema::create('categorizable', function (Blueprint $table) {
            $table->uuid('categorizable_id');
            $table->string('categorizable_type');
            $table->uuid('category_id');
            $table->timestamps();

            $table->primary(['categorizable_id', 'categorizable_type', 'category_id']);
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->index(['categorizable_id', 'categorizable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorizable');
    }
};
