<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Event;
use App\Models\Feed;
use App\Models\Registration;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\User;
use App\Services\CacheService;
use Illuminate\Console\Command;

class CacheWarmup extends Command
{
    protected $signature = 'cache:warmup';
    protected $description = 'Préchauffer le cache avec les données les plus utilisées';

    public function handle()
    {
        $this->info('Préchauffage du cache...');

        // 1. Cache des catégories
        $this->info('  - Catégories...');
        CacheService::remember('categories_list', function () {
            return Category::distinct()->get(['id', 'name', 'type']);
        }, CacheService::TTL_DAY);

        // 2. Cache de la page d'accueil (feeds publics)
        $this->info('  - Feeds publics...');
        $cacheKey = CacheService::feedsKey([]);
        CacheService::remember($cacheKey, function () {
            $query = Feed::where('isPrivate', false)
                ->where('status', 'publiée')
                ->with(['feedable.categories', 'user']);

            $query->where(function($q) {
                $now = now();
                $q->whereHasMorph('feedable', [Event::class], function($sub) use ($now) {
                    $sub->where('start_date', '>=', $now);
                })->orWhereHasMorph('feedable', [Training::class], function($sub) use ($now) {
                    $sub->whereRaw('COALESCE(end_date, start_date) >= ?', [$now]);
                });
            });

            $limit = config('scaling.limits.feeds_homepage', 200);
            $publicFeeds = $query->latest()->limit($limit)->get();

            $eventFeeds = $publicFeeds->filter(fn($f) => $f->feedable_type === 'App\Models\Event');
            $trainingFeeds = $publicFeeds->filter(fn($f) => $f->feedable_type === 'App\Models\Training');

            $upcomingCount = $publicFeeds->filter(function($feed) {
                if (!$feed->feedable) return false;
                $date = $feed->feedable->end_date ?? $feed->feedable->start_date;
                return \Carbon\Carbon::parse($date)->isFuture();
            })->count();

            $freeCount = $publicFeeds->filter(function($feed) {
                if (!$feed->feedable) return false;
                if ($feed->feedable_type === 'App\Models\Event') {
                    return $feed->feedable->amount <= 0;
                }
                return !$feed->feedable->canPaid;
            })->count();

            $swiperEvents = $eventFeeds->filter(fn($f) => $f->feedable && $f->feedable->file)->take(10);

            return compact('eventFeeds', 'trainingFeeds', 'upcomingCount', 'freeCount', 'swiperEvents');
        }, CacheService::TTL_MEDIUM);

        // 3. Cache admin stats
        $this->info('  - Stats admin...');
        CacheService::remember('admin_dashboard_stats', function () {
            return [
                'totalUsers' => User::count(),
                'totalTrainings' => Training::count(),
                'totalEvents' => Event::count(),
                'totalRegistrations' => Registration::count(),
            ];
        }, CacheService::TTL_MEDIUM);

        // 4. Cache KPIs admin
        $this->info('  - KPIs admin...');
        CacheService::remember('admin_kpis', function () {
            $since30 = now()->subDays(30);
            
            return [
                'subscriptionsTotal' => Subscription::count(),
                'subscriptionsActive' => Subscription::where('status', 'active')
                    ->where('payment_status', 'paid')
                    ->where('end_date', '>', now())
                    ->count(),
                'subscriptionsRevenueTotal' => (float) Subscription::where('payment_status', 'paid')->sum('amount'),
                'registrationsPaidTotal' => Registration::where('payment_status', 'paid')->count(),
                'registrationsRevenueTotal' => (float) Registration::where('payment_status', 'paid')->sum('amount_paid'),
                'totalRevenueTotal' => (float) Subscription::where('payment_status', 'paid')->sum('amount') + 
                                       (float) Registration::where('payment_status', 'paid')->sum('amount_paid'),
                'publishedFeeds' => (int) Feed::where('status', 'publiée')->count(),
                'draftFeeds' => (int) Feed::where('status', 'brouillon')->count(),
            ];
        }, CacheService::TTL_MEDIUM);

        $this->info('Cache préchauffé avec succès !');
        return Command::SUCCESS;
    }
}
