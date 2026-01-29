<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CacheClear extends Command
{
    protected $signature = 'cache:clear-all {--keep-config : Garder le cache de config}';
    protected $description = 'Nettoyer tous les caches de l\'application';

    public function handle()
    {
        $this->info('Nettoyage des caches...');

        // Cache applicatif
        $this->info('  - Cache applicatif...');
        Cache::flush();

        // Cache des feeds
        $this->info('  - Cache des feeds...');
        CacheService::clearFeedsCache();

        // Cache admin
        $this->info('  - Cache admin...');
        CacheService::clearAdminCache();

        if (!$this->option('keep-config')) {
            // Caches Laravel
            $this->info('  - Cache config...');
            $this->call('config:clear');

            $this->info('  - Cache routes...');
            $this->call('route:clear');

            $this->info('  - Cache vues...');
            $this->call('view:clear');

            $this->info('  - Cache events...');
            $this->call('event:clear');
        }

        $this->info('Tous les caches ont été nettoyés !');
        return Command::SUCCESS;
    }
}
