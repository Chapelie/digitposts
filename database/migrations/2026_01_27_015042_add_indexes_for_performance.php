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
        // Index pour la table feeds
        Schema::table('feeds', function (Blueprint $table) {
            if (!$this->hasIndex('feeds', 'feeds_user_id_index')) {
                $table->index('user_id', 'feeds_user_id_index');
            }
            if (!$this->hasIndex('feeds', 'feeds_status_index')) {
                $table->index('status', 'feeds_status_index');
            }
            if (!$this->hasIndex('feeds', 'feeds_is_private_index')) {
                $table->index('isPrivate', 'feeds_is_private_index');
            }
            // Index composite pour les requêtes fréquentes
            if (!$this->hasIndex('feeds', 'feeds_status_private_index')) {
                $table->index(['status', 'isPrivate'], 'feeds_status_private_index');
            }
        });

        // Index pour la table registrations
        Schema::table('registrations', function (Blueprint $table) {
            if (!$this->hasIndex('registrations', 'registrations_user_id_index')) {
                $table->index('user_id', 'registrations_user_id_index');
            }
            if (!$this->hasIndex('registrations', 'registrations_feed_id_index')) {
                $table->index('feed_id', 'registrations_feed_id_index');
            }
            if (!$this->hasIndex('registrations', 'registrations_status_index')) {
                $table->index('status', 'registrations_status_index');
            }
            if (!$this->hasIndex('registrations', 'registrations_payment_status_index')) {
                $table->index('payment_status', 'registrations_payment_status_index');
            }
            if (!$this->hasIndex('registrations', 'registrations_payment_transaction_id_index')) {
                $table->index('payment_transaction_id', 'registrations_payment_transaction_id_index');
            }
            // Index composite
            if (!$this->hasIndex('registrations', 'registrations_user_feed_index')) {
                $table->index(['user_id', 'feed_id'], 'registrations_user_feed_index');
            }
        });

        // Index pour la table favorites
        Schema::table('favorites', function (Blueprint $table) {
            if (!$this->hasIndex('favorites', 'favorites_user_id_index')) {
                $table->index('user_id', 'favorites_user_id_index');
            }
            if (!$this->hasIndex('favorites', 'favorites_feed_id_index')) {
                $table->index('feed_id', 'favorites_feed_id_index');
            }
            // Index unique composite pour éviter les doublons
            if (!$this->hasIndex('favorites', 'favorites_user_feed_unique')) {
                $table->unique(['user_id', 'feed_id'], 'favorites_user_feed_unique');
            }
        });

        // Index pour events et trainings
        Schema::table('events', function (Blueprint $table) {
            if (!$this->hasIndex('events', 'events_start_date_index')) {
                $table->index('start_date', 'events_start_date_index');
            }
        });

        Schema::table('trainings', function (Blueprint $table) {
            if (!$this->hasIndex('trainings', 'trainings_start_date_index')) {
                $table->index('start_date', 'trainings_start_date_index');
            }
            if (!$this->hasIndex('trainings', 'trainings_end_date_index')) {
                $table->index('end_date', 'trainings_end_date_index');
            }
        });

        // Index pour categorizable
        Schema::table('categorizable', function (Blueprint $table) {
            if (!$this->hasIndex('categorizable', 'categorizable_categorizable_id_index')) {
                $table->index('categorizable_id', 'categorizable_categorizable_id_index');
            }
            if (!$this->hasIndex('categorizable', 'categorizable_category_id_index')) {
                $table->index('category_id', 'categorizable_category_id_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feeds', function (Blueprint $table) {
            $table->dropIndex('feeds_user_id_index');
            $table->dropIndex('feeds_status_index');
            $table->dropIndex('feeds_is_private_index');
            $table->dropIndex('feeds_status_private_index');
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropIndex('registrations_user_id_index');
            $table->dropIndex('registrations_feed_id_index');
            $table->dropIndex('registrations_status_index');
            $table->dropIndex('registrations_payment_status_index');
            $table->dropIndex('registrations_payment_transaction_id_index');
            $table->dropIndex('registrations_user_feed_index');
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->dropIndex('favorites_user_id_index');
            $table->dropIndex('favorites_feed_id_index');
            $table->dropUnique('favorites_user_feed_unique');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('events_start_date_index');
        });

        Schema::table('trainings', function (Blueprint $table) {
            $table->dropIndex('trainings_start_date_index');
            $table->dropIndex('trainings_end_date_index');
        });

        Schema::table('categorizable', function (Blueprint $table) {
            $table->dropIndex('categorizable_categorizable_id_index');
            $table->dropIndex('categorizable_category_id_index');
        });
    }

    /**
     * Vérifier si un index existe déjà
     */
    private function hasIndex($table, $indexName): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();
        
        if ($connection->getDriverName() === 'mysql') {
            $result = $connection->select(
                "SELECT COUNT(*) as count FROM information_schema.statistics 
                 WHERE table_schema = ? AND table_name = ? AND index_name = ?",
                [$database, $table, $indexName]
            );
            return $result[0]->count > 0;
        }
        
        // Pour SQLite, on assume que l'index n'existe pas pour éviter les erreurs
        return false;
    }
};
