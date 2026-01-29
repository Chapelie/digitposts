<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Feed;
use App\Models\Registration;
use App\Models\Training;
use App\Models\User;
use App\Models\Subscription;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    public function index()
    {
        // Cache les statistiques pour 30 minutes
        $stats = CacheService::remember('admin_dashboard_stats', function () {
            return [
                'totalUsers' => User::count(),
                'totalTrainings' => Training::count(),
                'totalEvents' => Event::count(),
                'totalRegistrations' => Registration::count(),
            ];
        }, CacheService::TTL_MEDIUM);

        // KPI business (abonnements + revenus) - cache 30 minutes
        $kpis = CacheService::remember('admin_kpis', function () {
            $since30 = now()->subDays(30);

            $subscriptionsTotal = Subscription::count();
            $subscriptionsActive = Subscription::where('status', 'active')
                ->where('payment_status', 'paid')
                ->where('end_date', '>', now())
                ->count();

            $subscriptionsRevenueTotal = (float) Subscription::where('payment_status', 'paid')->sum('amount');
            $subscriptionsRevenue30d = (float) Subscription::where('payment_status', 'paid')
                ->where(function ($q) use ($since30) {
                    $q->whereNotNull('payment_date')->where('payment_date', '>=', $since30)
                      ->orWhere(function ($q2) use ($since30) {
                          $q2->whereNull('payment_date')->where('created_at', '>=', $since30);
                      });
                })
                ->sum('amount');

            $registrationsPaidTotal = Registration::where('payment_status', 'paid')->count();
            $registrationsRevenueTotal = (float) Registration::where('payment_status', 'paid')->sum('amount_paid');
            $registrationsRevenue30d = (float) Registration::where('payment_status', 'paid')
                ->where(function ($q) use ($since30) {
                    $q->whereNotNull('payment_date')->where('payment_date', '>=', $since30)
                      ->orWhere(function ($q2) use ($since30) {
                          $q2->whereNull('payment_date')->where('created_at', '>=', $since30);
                      });
                })
                ->sum('amount_paid');

            $totalRevenueTotal = $subscriptionsRevenueTotal + $registrationsRevenueTotal;
            $totalRevenue30d = $subscriptionsRevenue30d + $registrationsRevenue30d;

            $totalRegistrations = (int) Registration::count();
            $registrationPaidRate = $totalRegistrations > 0
                ? round(($registrationsPaidTotal / $totalRegistrations) * 100, 1)
                : 0.0;

            $activeSubscribersUsers = (int) Subscription::where('status', 'active')
                ->where('payment_status', 'paid')
                ->where('end_date', '>', now())
                ->distinct('user_id')
                ->count('user_id');

            $totalUsers = (int) User::count();
            $subscriptionPenetration = $totalUsers > 0
                ? round(($activeSubscribersUsers / $totalUsers) * 100, 1)
                : 0.0;

            $publishedFeeds = (int) Feed::where('status', 'publiée')->count();
            $draftFeeds = (int) Feed::where('status', 'brouillon')->count();

            return compact(
                'subscriptionsTotal',
                'subscriptionsActive',
                'subscriptionsRevenueTotal',
                'subscriptionsRevenue30d',
                'registrationsPaidTotal',
                'registrationsRevenueTotal',
                'registrationsRevenue30d',
                'totalRevenueTotal',
                'totalRevenue30d',
                'registrationPaidRate',
                'activeSubscribersUsers',
                'subscriptionPenetration',
                'publishedFeeds',
                'draftFeeds'
            );
        }, CacheService::TTL_MEDIUM);

        // Données récentes (cachées 5 minutes)
        $recentUsers = CacheService::remember('admin_recent_users', function () {
            return User::latest()->take(5)->get();
        }, CacheService::TTL_SHORT);

        $recentFeeds = CacheService::remember('admin_recent_feeds', function () {
            return Feed::with(['feedable', 'user'])->latest()->take(5)->get();
        }, CacheService::TTL_SHORT);

        $recentRegistrations = CacheService::remember('admin_recent_registrations', function () {
            return Registration::with(['feed.feedable', 'user'])->latest()->take(5)->get();
        }, CacheService::TTL_SHORT);

        // Statistiques par mois (cachées 1 heure)
        $monthlyStats = CacheService::remember('admin_monthly_stats', function () {
            return $this->getMonthlyStats();
        }, CacheService::TTL_LONG);

        // Top créateurs (cachés 30 minutes)
        $topCreators = CacheService::remember('admin_top_creators', function () {
            return User::withCount(['feeds', 'registrations'])
                ->orderBy('feeds_count', 'desc')
                ->take(5)
                ->get();
        }, CacheService::TTL_MEDIUM);

        // Activités populaires (cachées 30 minutes)
        $popularActivities = CacheService::remember('admin_popular_activities', function () {
            return Feed::with(['feedable', 'registrations'])
                ->withCount('registrations')
                ->orderBy('registrations_count', 'desc')
                ->take(5)
                ->get();
        }, CacheService::TTL_MEDIUM);

        return view('admin.dashboard', array_merge($stats, [
            'kpis' => $kpis,
            'recentUsers' => $recentUsers,
            'recentFeeds' => $recentFeeds,
            'recentRegistrations' => $recentRegistrations,
            'monthlyStats' => $monthlyStats,
            'topCreators' => $topCreators,
            'popularActivities' => $popularActivities
        ]));
    }

    public function users()
    {
        $users = User::withCount(['feeds', 'registrations'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function activities()
    {
        $activities = Feed::with(['feedable', 'user', 'registrations'])
            ->withCount('registrations')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.activities.index', compact('activities'));
    }

    public function registrations()
    {
        $registrations = Registration::with(['feed.feedable', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.registrations.index', compact('registrations'));
    }

    private function getMonthlyStats()
    {
        $stats = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('M Y');
            
            $stats[$month] = [
                'users' => User::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'trainings' => Training::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'events' => Event::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'registrations' => Registration::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }
        
        return $stats;
    }
}
