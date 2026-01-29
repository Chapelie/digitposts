<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    // Durées de cache en secondes
    public const TTL_SHORT = 300;      // 5 minutes
    public const TTL_MEDIUM = 1800;    // 30 minutes
    public const TTL_LONG = 3600;      // 1 heure
    public const TTL_DAY = 86400;      // 24 heures
    public const TTL_WEEK = 604800;    // 7 jours

    // Préfixes de cache
    public const PREFIX_FEEDS = 'feeds_';
    public const PREFIX_USER = 'user_';
    public const PREFIX_ADMIN = 'admin_';
    public const PREFIX_CATEGORIES = 'categories_';

    /**
     * Générer une clé de cache pour les feeds avec paramètres
     */
    public static function feedsKey(array $params = []): string
    {
        return self::PREFIX_FEEDS . md5(json_encode($params));
    }

    /**
     * Générer une clé de cache pour un utilisateur
     */
    public static function userKey(string $userId, string $type = 'dashboard'): string
    {
        return self::PREFIX_USER . $userId . '_' . $type;
    }

    /**
     * Générer une clé de cache admin
     */
    public static function adminKey(string $type = 'stats'): string
    {
        return self::PREFIX_ADMIN . $type;
    }

    /**
     * Nettoyer le cache des feeds
     */
    public static function clearFeedsCache(): void
    {
        // Nettoyer les clés de cache connues
        $keysToForget = [
            'feeds_index_*',
            'categories_list',
            'categories_all',
            'home_page_data',
            'swiper_events',
        ];

        foreach ($keysToForget as $key) {
            if (str_contains($key, '*')) {
                // Pour les patterns, on utilise flush si nécessaire
                continue;
            }
            Cache::forget($key);
        }

        // Nettoyer le cache avec patterns spécifiques
        Cache::forget('feeds_index_' . md5(json_encode([])));
        Cache::forget('feeds_index_' . md5(json_encode(['category' => null, 'free' => null])));
        Cache::forget('feeds_index_' . md5(json_encode(['free' => 'true'])));
    }

    /**
     * Nettoyer le cache d'un utilisateur
     */
    public static function clearUserCache(string $userId): void
    {
        $types = ['dashboard', 'stats', 'registrations', 'favorites', 'creator_dashboard', 'campaigns_list'];
        foreach ($types as $type) {
            Cache::forget(self::userKey($userId, $type));
        }
        // Anciens formats de clé pour compatibilité
        Cache::forget('creator_dashboard_' . $userId);
    }

    /**
     * Nettoyer le cache admin
     */
    public static function clearAdminCache(): void
    {
        $types = ['stats', 'kpis', 'monthly', 'creators', 'popular'];
        foreach ($types as $type) {
            Cache::forget(self::adminKey($type));
        }
        Cache::forget('admin_dashboard_stats');
        Cache::forget('admin_kpis');
        Cache::forget('admin_monthly_stats');
        Cache::forget('admin_top_creators');
        Cache::forget('admin_popular_activities');
    }

    /**
     * Mettre en cache avec TTL automatique
     */
    public static function remember(string $key, $callback, int $ttl = null)
    {
        $ttl = $ttl ?? self::TTL_MEDIUM;
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Mettre en cache pour toujours (jusqu'à invalidation)
     */
    public static function rememberForever(string $key, $callback)
    {
        return Cache::rememberForever($key, $callback);
    }

    /**
     * Récupérer du cache ou null
     */
    public static function get(string $key, $default = null)
    {
        return Cache::get($key, $default);
    }

    /**
     * Mettre en cache
     */
    public static function put(string $key, $value, int $ttl = null): void
    {
        $ttl = $ttl ?? self::TTL_MEDIUM;
        Cache::put($key, $value, $ttl);
    }

    /**
     * Supprimer du cache
     */
    public static function forget(string $key): void
    {
        Cache::forget($key);
    }

    /**
     * Vérifier si une clé existe
     */
    public static function has(string $key): bool
    {
        return Cache::has($key);
    }
}
