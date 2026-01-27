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
        Schema::table('users', function (Blueprint $table) {
            // Ajouter l'index unique sur phone si la colonne existe déjà
            if (Schema::hasColumn('users', 'phone')) {
                // Supprimer les doublons avant d'ajouter l'index unique
                \DB::statement('UPDATE users SET phone = NULL WHERE phone = "" OR phone IS NULL');
                
                // Ajouter l'index unique (nullable pour permettre les valeurs NULL)
                $table->string('phone')->nullable()->unique()->change();
            } else {
                $table->string('phone')->nullable()->unique()->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropUnique(['phone']);
                $table->string('phone')->nullable()->change();
            }
        });
    }
};
