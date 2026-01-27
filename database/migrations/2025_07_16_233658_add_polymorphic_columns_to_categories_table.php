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
        Schema::table('categories', function (Blueprint $table) {
            $table->uuid('categorizable_id')->nullable()->after('type');
            $table->string('categorizable_type')->nullable()->after('categorizable_id');
            $table->index(['categorizable_id', 'categorizable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['categorizable_id', 'categorizable_type']);
            $table->dropColumn(['categorizable_id', 'categorizable_type']);
        });
    }
};
