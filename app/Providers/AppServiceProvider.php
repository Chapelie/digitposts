<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Une seule origine HTTPS en prod → URLs générées alignées (évite écarts CSRF / cookies)
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        // Prévenir les problèmes de lazy loading (N+1) en développement
        Model::preventLazyLoading(!$this->app->isProduction());
        
        // Log des requêtes lentes en développement (> 500ms)
        if (!$this->app->isProduction()) {
            DB::listen(function ($query) {
                if ($query->time > 500) {
                    \Log::warning('Requête lente détectée', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time . 'ms'
                    ]);
                }
            });
        }
    }
}
