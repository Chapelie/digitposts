<?php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Feed;
use App\Models\Registration;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class CreatorController extends Controller{
    public function index()
    {
        $user = Auth::user();
        $cacheKey = 'creator_dashboard_' . $user->id;

        // Cache pour 5 minutes
        $data = Cache::remember($cacheKey, 300, function () use ($user) {
            // Récupérer les feeds de l'utilisateur avec les relations (optimisé)
            $feeds = Feed::with(['feedable', 'user', 'registrations'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            // Compter les campagnes en une seule requête optimisée
            $trainingsCount = Feed::where('user_id', $user->id)
                ->where('feedable_type', 'App\Models\Training')
                ->count();

            $eventsCount = Feed::where('user_id', $user->id)
                ->where('feedable_type', 'App\Models\Event')
                ->count();

            $totalCampaigns = $trainingsCount + $eventsCount;

            // Compter les inscriptions en une seule requête
            $totalRegistrations = Registration::whereHas('feed', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count();

            // Récupérer les campagnes à venir (optimisé avec une seule requête via Feed)
            $upcomingFeeds = Feed::with(['feedable'])
                ->where('user_id', $user->id)
                ->whereHasMorph('feedable', [Training::class, Event::class], function($query) {
                    $query->where('start_date', '>', now());
                })
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get();

            $upcomingCampaigns = $upcomingFeeds->map(function($feed) {
                $feedable = $feed->feedable;
                $feedable->type = $feed->feedable_type === 'App\Models\Training' ? 'training' : 'event';
                return $feedable;
            })->sortBy('start_date')->take(3);

            // Calculer le taux de complétion (optimisé)
            $completedCampaigns = Feed::where('user_id', $user->id)
                ->where(function($query) {
                    $query->whereHasMorph('feedable', [Training::class], function($q) {
                        $q->where('end_date', '<', now());
                    })->orWhereHasMorph('feedable', [Event::class], function($q) {
                        $q->where('start_date', '<', now());
                    });
                })
                ->count();

            $completionRate = $totalCampaigns > 0 ? round(($completedCampaigns / $totalCampaigns) * 100) : 0;

            // Récupérer les inscriptions récentes
            $recentRegistrations = Registration::with(['feed.feedable', 'user'])
                ->whereHas('feed', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            return [
                'feeds' => $feeds,
                'totalCampaigns' => $totalCampaigns,
                'totalRegistrations' => $totalRegistrations,
                'upcomingCampaigns' => $upcomingCampaigns,
                'completionRate' => $completionRate,
                'recentRegistrations' => $recentRegistrations,
                'trainingsCount' => $trainingsCount,
                'eventsCount' => $eventsCount
            ];
        });

        return view('dashboard.index', array_merge($data, ['user' => $user]));
    }

    public function campaignIndex()
    {
        $user = Auth::user();

        $campaigns = Feed::with(['feedable', 'user', 'registrations'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Récupérer toutes les catégories depuis la base de données (cached)
        $categories = Cache::remember('categories_list', 3600, function () {
            return Category::distinct()->get(['id', 'name', 'type']);
        });

        return view('campagnes.index', compact('campaigns', 'categories'));
    }

    public function campaignCreate(){
         // Récupérer toutes les catégories existantes (cached)
        $categories = Cache::remember('categories_list', 3600, function () {
            return Category::orderBy('name')->get();
        });
        
        return view('campagnes.create', compact('categories'));
     }

     public function campaignStore(Request $request){
            $user = Auth::user();
             $request->validate([
                 'type' => 'required|in:event,training',
                 'title' => 'required|string|max:255',
                 'description' => 'nullable|string|max:5000',
                 'start_date' => 'required|date|after_or_equal:today',
                 'end_date' => 'nullable|date|after:start_date',
                 'amount' => 'nullable|numeric|min:0|max:999999999',
                 'file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:5120', // 5MB max
                 'categories' => 'nullable|array|max:10',
                 'categories.*' => 'nullable|string|max:255',
                 'new_categories' => 'nullable|string|max:500',
                 'location' => 'nullable|string|max:255',
                 'place' => 'nullable|string|max:255',
                 'link' => 'nullable|url|max:500',
             ], [
                 'start_date.after_or_equal' => 'La date de début doit être aujourd\'hui ou dans le futur.',
                 'end_date.after' => 'La date de fin doit être après la date de début.',
                 'file.max' => 'Le fichier ne doit pas dépasser 5MB.',
                 'categories.max' => 'Vous ne pouvez sélectionner que 10 catégories maximum.',
             ]);
         // On stocke le fichier dans un répertoire 'uploads' et on récupère le chemin
         $filePath = null;
         if ($request->hasFile('file')) {
             $filePath = $request->file('file')->store('feed/', 'public');
         }

         if ($request->type === 'event') {
             $data = $request->only(['title', 'description', 'start_date']);
             // Gérer le montant pour les événements
             $data['amount'] = $request->filled('amount') && $request->amount > 0 ? $request->amount : 0;
             $data['file'] = $filePath; // on ajoute manuellement le fichier
             $feedable = Event::create($data);
         } else {
             $data = $request->only([
                 'title', 'description', 'start_date', 'end_date',
                 'location', 'place', 'canPaid', 'link'
             ]);
             // Gérer le montant pour les formations
             $data['amount'] = $request->filled('amount') && $request->amount > 0 ? $request->amount : 0;
             $data['file'] = $filePath;
             $feedable = Training::create($data);
         }

         // Gérer les catégories (optimisé avec une seule requête)
         $categoryIds = [];
         
         // Ajouter les catégories existantes sélectionnées
         if ($request->has('categories') && is_array($request->categories)) {
             $categoryIds = array_filter(array_map('trim', $request->categories));
         }
         
         // Créer de nouvelles catégories si spécifiées (optimisé)
         if ($request->filled('new_categories')) {
             $newCategoryNames = array_filter(array_map('trim', explode(',', $request->new_categories)));
             $categoriesToCreate = [];
             
             foreach ($newCategoryNames as $categoryName) {
                 if (!empty($categoryName) && strlen($categoryName) <= 255) {
                     $categoriesToCreate[] = [
                         'name' => $categoryName,
                         'type' => $request->type,
                         'created_at' => now(),
                         'updated_at' => now(),
                     ];
                 }
             }
             
             if (!empty($categoriesToCreate)) {
                 // Utiliser insertOrIgnore pour éviter les doublons
                 foreach ($categoriesToCreate as $catData) {
                     $category = Category::firstOrCreate(
                         ['name' => $catData['name'], 'type' => $catData['type']],
                         $catData
                     );
                     $categoryIds[] = $category->id;
                 }
             }
         }
         
         // Attacher les catégories à l'activité (optimisé avec une seule requête)
         if (!empty($categoryIds)) {
             $categoryIds = array_unique($categoryIds); // Éviter les doublons
             $feedable->attachCategories($categoryIds);
         }

         // Create the feed
             $feed = new Feed([
                 'isPrivate' => $request->isPrivate ?? false,
                 'status' => 'publiée',
                 'user_id' => $user->id
             ]);
             $feedable->feed()->save($feed);

             // Nettoyer le cache des feeds
             Cache::forget('feeds_index_' . md5(json_encode([])));
             Cache::forget('categories_list');
             Cache::forget('creator_dashboard_' . $user->id);

             return redirect()->route('home')->with('success', 'Activity created successfully');
         }

    public function campaignRegistrations($uuid)
    {
        $user = Auth::user();
        
        // Récupérer la campagne avec ses inscriptions
        $feed = Feed::where('id', $uuid)
            ->where('user_id', $user->id)
            ->with(['feedable', 'registrations.user'])
            ->firstOrFail();

        // Récupérer les inscriptions avec les informations utilisateur
        $registrations = $feed->registrations()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistiques
        $totalRegistrations = $registrations->count();
        $confirmedRegistrations = $registrations->where('status', Registration::STATUS_CONFIRMED)->count();
        $pendingRegistrations = $registrations->where('status', Registration::STATUS_PENDING)->count();
        $cancelledRegistrations = $registrations->where('status', Registration::STATUS_CANCELLED)->count();

        return view('campagnes.registrations', compact(
            'feed', 
            'registrations', 
            'totalRegistrations', 
            'confirmedRegistrations', 
            'pendingRegistrations', 
            'cancelledRegistrations'
        ));
    }

    public function settings()
    {
        $user = Auth::user();
        return view('dashboard.settings', compact('user'));
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'organization' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'location' => 'nullable|string|max:255',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
            'new_password_confirmation' => 'nullable|min:8',
        ]);

        // Vérifier le mot de passe actuel si un nouveau mot de passe est fourni
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
            }
        }

        // Mettre à jour les informations de base
        $user->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $request->phone,
            'organization' => $request->organization,
            'bio' => $request->bio,
            'website' => $request->website,
            'location' => $request->location,
        ]);

        // Mettre à jour le mot de passe si fourni
        if ($request->filled('new_password')) {
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
        }

        return redirect()->route('settings')->with('success', 'Paramètres mis à jour avec succès !');
    }
}
