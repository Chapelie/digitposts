<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Feed;
use App\Models\Registration;
use App\Models\Training;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Ajouter middleware pour vérifier si l'utilisateur est super admin
        // $this->middleware('superadmin');
    }

    public function index()
    {
        // Cache les statistiques pour 5 minutes
        $cacheKey = 'admin_dashboard_stats';
        $stats = Cache::remember($cacheKey, 300, function () {
            return [
                'totalUsers' => User::count(),
                'totalTrainings' => Training::count(),
                'totalEvents' => Event::count(),
                'totalRegistrations' => Registration::count(),
            ];
        });

        // Données récentes (non cachées car doivent être à jour)
        $recentUsers = User::latest()->take(5)->get();
        $recentFeeds = Feed::with(['feedable', 'user'])->latest()->take(5)->get();
        $recentRegistrations = Registration::with(['feed.feedable', 'user'])->latest()->take(5)->get();

        // Statistiques par mois (cachées 10 minutes)
        $monthlyStats = Cache::remember('admin_monthly_stats', 600, function () {
            return $this->getMonthlyStats();
        });

        // Top créateurs (cachés 5 minutes)
        $topCreators = Cache::remember('admin_top_creators', 300, function () {
            return User::withCount(['feeds', 'registrations'])
                ->orderBy('feeds_count', 'desc')
                ->take(5)
                ->get();
        });

        // Activités populaires (cachées 5 minutes)
        $popularActivities = Cache::remember('admin_popular_activities', 300, function () {
            return Feed::with(['feedable', 'registrations'])
                ->withCount('registrations')
                ->orderBy('registrations_count', 'desc')
                ->take(5)
                ->get();
        });

        return view('admin.dashboard', array_merge($stats, [
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
