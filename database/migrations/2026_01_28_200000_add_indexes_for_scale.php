<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Index et optimisations pour supporter un grand nombre d'utilisateurs.
     */
    public function up(): void
    {
        // Users : requêtes "derniers inscrits", tri par date
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $this->addIndexIfNotExists($table, 'users', 'users_created_at_index', 'created_at');
            });
        }

        // Feeds : relation polymorphique + tri chronologique
        if (Schema::hasTable('feeds')) {
            Schema::table('feeds', function (Blueprint $table) {
                $this->addIndexIfNotExists($table, 'feeds', 'feeds_feedable_index', ['feedable_id', 'feedable_type']);
                $this->addIndexIfNotExists($table, 'feeds', 'feeds_created_at_index', 'created_at');
            });
        }

        // Subscriptions : lookup webhook par transaction_id, requêtes actives par end_date
        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $this->addIndexIfNotExists($table, 'subscriptions', 'subscriptions_payment_transaction_id_index', 'payment_transaction_id');
                $this->addIndexIfNotExists($table, 'subscriptions', 'subscriptions_end_date_index', 'end_date');
            });
        }

    }

    public function down(): void
    {
        $this->dropIndexSafe('users', 'users_created_at_index');
        $this->dropIndexSafe('feeds', 'feeds_feedable_index');
        $this->dropIndexSafe('feeds', 'feeds_created_at_index');
        $this->dropIndexSafe('subscriptions', 'subscriptions_payment_transaction_id_index');
        $this->dropIndexSafe('subscriptions', 'subscriptions_end_date_index');
    }

    private function addIndexIfNotExists(Blueprint $table, string $t, string $name, $columns): void
    {
        if ($this->indexExists($t, $name)) {
            return;
        }
        $table->index($columns, $name);
    }

    private function dropIndexSafe(string $table, string $name): void
    {
        if (!$this->indexExists($table, $name)) {
            return;
        }
        Schema::table($table, function (Blueprint $t) use ($name) {
            $t->dropIndex($name);
        });
    }

    private function indexExists(string $table, string $name): bool
    {
        $conn = Schema::getConnection();
        $driver = $conn->getDriverName();

        if ($driver === 'sqlite') {
            $idx = $conn->select("SELECT name FROM sqlite_master WHERE type='index' AND tbl_name=? AND name=?", [$table, $name]);
            return count($idx) > 0;
        }

        if ($driver === 'mysql') {
            $db = $conn->getDatabaseName();
            $r = $conn->select(
                "SELECT COUNT(*) as c FROM information_schema.statistics WHERE table_schema=? AND table_name=? AND index_name=?",
                [$db, $table, $name]
            );
            return ($r[0]->c ?? 0) > 0;
        }

        return false;
    }
};
