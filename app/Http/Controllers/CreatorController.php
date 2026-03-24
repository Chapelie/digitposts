<?php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Feed;
use App\Models\Registration;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\CacheService;
use App\Support\CampaignWriteMac;

class CreatorController extends Controller
{
    private function canManageFeed(Feed $feed, $user): bool
    {
        return $user && ($user->isAdmin() || (string) $feed->user_id === (string) $user->id);
    }

    private function managedFeedQuery($user)
    {
        $query = Feed::query();
        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public function index()
    {
        $user = Auth::user();
        $cacheKey = CacheService::userKey($user->id, 'creator_dashboard');

        // Cache pour 10 minutes
        $data = CacheService::remember($cacheKey, function () use ($user) {
            // Récupérer les feeds de l'utilisateur avec les relations (optimisé)
            $feeds = Feed::with(['feedable', 'user'])
                ->withCount('registrations')
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
                ->withCount('registrations')
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
                $feedable->setRelation('feed', $feed);

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
        }, CacheService::TTL_MEDIUM);

        return view('dashboard.index', array_merge($data, ['user' => $user]));
    }

    public function campaignIndex()
    {
        $user = Auth::user();
        $cacheKey = CacheService::userKey($user->id, 'campaigns_list_'.($user->isAdmin() ? 'admin' : 'owner'));

        $limit = config('scaling.limits.campaigns_per_creator', 500);
        $campaigns = CacheService::remember($cacheKey, function () use ($user, $limit) {
            return $this->managedFeedQuery($user)
                ->with(['feedable.categories', 'user'])
                ->withCount('registrations')
                ->whereHasMorph('feedable', [Event::class, Training::class])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->filter(fn($feed) => $feed->feedable !== null);
        }, CacheService::TTL_MEDIUM);

        // Récupérer toutes les catégories depuis la base de données (cached 24h)
        $categories = CacheService::remember('categories_list', function () {
            return Category::distinct()->get(['id', 'name', 'type']);
        }, CacheService::TTL_DAY);

        return view('campagnes.index', compact('campaigns', 'categories'));
    }

    public function campaignCreate(){
         // Récupérer toutes les catégories existantes (cached 24h)
        $categories = CacheService::remember('categories_all', function () {
            return Category::orderBy('name')->get();
        }, CacheService::TTL_DAY);

        $zones = config('digitposts.zones', []);
        $tarifsDiffusion = config('digitposts.tarifs_diffusion', []);
        
        $cfTs = time();
        $cfMac = CampaignWriteMac::forStore(Auth::id(), $cfTs);

        return view('campagnes.create', compact('categories', 'zones', 'tarifsDiffusion', 'cfTs', 'cfMac'));
     }

     public function campaignStore(Request $request){
            $user = Auth::user();

            if (! CampaignWriteMac::validStoreOrUpdate($request, $user->id)) {
                return redirect()->route('campaigns.create')
                    ->with('error', 'Le formulaire a expiré ou est invalide. Rechargez la page « Créer une campagne » et réessayez.')
                    ->withInput($request->except(['file', '_token']));
            }

            // Si l'utilisateur choisit "event", on ignore end_date côté serveur.
            // (Le champ peut rester rempli même s'il est masqué dans le formulaire.)
            if ($request->input('type') === 'event') {
                $request->merge(['end_date' => null]);
            }

            $validator = Validator::make($request->all(), [
                'type' => 'required|in:event,training',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:5000',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'nullable|date|after:start_date',
                'amount' => 'nullable|numeric|min:0|max:999999999',
                'file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
                'categories' => 'nullable|array|max:10',
                'categories.*' => 'nullable|string|max:255',
                'new_categories' => 'nullable|string|max:500',
                'location' => 'nullable|string|max:255',
                'place' => 'nullable|string|max:255',
                'link' => 'nullable|url|max:500',
                'status' => 'nullable|in:brouillon,publiée',
            ], [
                'start_date.after_or_equal' => 'La date de début doit être aujourd\'hui ou dans le futur.',
                'end_date.after' => 'La date de fin doit être après la date de début.',
                'file.max' => 'Le fichier ne doit pas dépasser 5MB.',
                'categories.max' => 'Vous ne pouvez sélectionner que 10 catégories maximum.',
            ]);

            if ($validator->fails()) {
                $to = $request->input('_campaign_create_source') === 'dashboard'
                    ? route('dashboard.campaigns.create')
                    : route('campaigns.create');

                return redirect()->to($to)
                    ->withErrors($validator)
                    ->withInput($request->except(['file']));
            }
         // Publier : si prix = 0 ET paiement désactivé (gratuit), pas d'abonnement requis
         $status = $request->input('status', 'brouillon');
         if ($status === 'publiée') {
             $amount = $request->filled('amount') ? (float) $request->amount : 0;
             $isFree = false;
             if ($request->type === 'event') {
                 $isFree = $amount <= 0;
             } else {
                 // Formation : gratuit si "Paiement possible" non coché OU montant à 0
                 $canPaid = $request->has('canPaid') && $request->input('canPaid');
                 $isFree = !$canPaid || $amount <= 0;
             }
             if (!$isFree && !Subscription::hasActiveSubscription($user->id, \App\Models\SubscriptionPlan::TYPE_CREATE_ACTIVITIES)) {
                 session(['url.intended' => route('campaigns.create')]);
                 return redirect()->route('subscriptions.checkout', ['plan' => 'create_activities'])
                     ->with('error', 'Pour publier une activité payante, abonnez-vous au plan "Création d\'activités". Les activités gratuites (prix 0, paiement désactivé) peuvent être publiées sans abonnement.');
             }
         }
         // On stocke le fichier dans un répertoire 'uploads' et on récupère le chemin
         $filePath = null;
         if ($request->hasFile('file')) {
             $filePath = $request->file('file')->store('feed/', 'public');
         }

         if ($request->type === 'event') {
             $data = $request->only(['title', 'description', 'start_date']);
             $data['location'] = $request->filled('zone') && $request->zone !== 'other' ? $request->zone : $request->input('location');
             // Gérer le montant pour les événements
             $data['amount'] = $request->filled('amount') && $request->amount > 0 ? $request->amount : 0;
             $data['file'] = $filePath; // on ajoute manuellement le fichier
             $feedable = Event::create($data);
         } else {
             $data = $request->only([
                 'title', 'description', 'start_date', 'end_date',
                 'place', 'link'
             ]);
             $data['location'] = $request->filled('zone') && $request->zone !== 'other' ? $request->zone : $request->input('location');
             $data['canPaid'] = $request->has('canPaid') && $request->input('canPaid');
             $data['amount'] = $request->filled('amount') && (float) $request->amount > 0 ? (float) $request->amount : 0;
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
                 'status' => $status,
                 'user_id' => $user->id
             ]);
             $feedable->feed()->save($feed);

            // Nettoyer le cache des feeds et de l'utilisateur
            CacheService::clearFeedsCache();
            CacheService::clearUserCache($user->id);
            CacheService::forget('categories_all');

             if ($status === 'publiée') {
                 return redirect()->route('home')->with('offer_published', true);
             }
             return redirect()->route('dashboard.campaigns')->with('success', 'Brouillon enregistré avec succès !');
         }

    /**
     * Point d’entrée GET /creator/campaigns/{uuid} (sans /edit) : lien court, favoris, retours paiement, etc.
     */
    public function campaignShowOrRedirect(string $uuid)
    {
        $feed = Feed::query()->where('id', $uuid)->first();
        if (! $feed) {
            abort(404);
        }

        $user = Auth::user();
        if ($this->canManageFeed($feed, $user)) {
            return redirect()->route('campaigns.edit', $uuid);
        }

        return redirect()->route('campaigns.show', $uuid);
    }

    public function campaignEdit(string $uuid)
    {
        $user = Auth::user();
        $feed = $this->managedFeedQuery($user)
            ->with(['feedable.categories', 'user'])
            ->where('id', $uuid)
            ->firstOrFail();

        $campaign = $feed->feedable;
        $categories = CacheService::remember('categories_all', function () {
            return Category::orderBy('name')->get();
        }, CacheService::TTL_DAY);
        $selectedCategoryIds = $campaign->categories->pluck('id')->all();
        $zones = config('digitposts.zones', []);
        $cfTs = time();
        $cfMac = CampaignWriteMac::forStore(Auth::id(), $cfTs);

        return view('campagnes.edit', compact('feed', 'campaign', 'categories', 'selectedCategoryIds', 'zones', 'cfTs', 'cfMac'));
    }

    public function campaignUpdate(Request $request, string $uuid)
    {
        $user = Auth::user();

        if (! CampaignWriteMac::validStoreOrUpdate($request, $user->id)) {
            return redirect()->route('campaigns.edit', $uuid)
                ->with('error', 'Le formulaire a expiré ou est invalide. Rechargez la page et réessayez.')
                ->withInput($request->except(['file', '_token']));
        }

        $feed = $this->managedFeedQuery($user)
            ->with(['feedable.categories'])
            ->where('id', $uuid)
            ->firstOrFail();
        $campaign = $feed->feedable;

        if ($request->input('type') === 'event') {
            $request->merge(['end_date' => null]);
        }

        $rules = [
            'type' => 'required|in:event,training',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'start_date' => 'required|date',
            'amount' => 'nullable|numeric|min:0|max:999999999',
            'status' => 'required|in:brouillon,publiée',
            'location' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'categories' => 'nullable|array|max:10',
            'categories.*' => 'nullable|string|max:255',
        ];

        if ($request->input('type') === 'training') {
            $rules = array_merge($rules, [
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'place' => 'nullable|string|max:255',
                'link' => 'nullable|url|max:500',
                'canPaid' => 'nullable|boolean',
            ]);
        }

        $data = $request->validate($rules);

        $status = $data['status'];
        if ($status === 'publiée') {
            $amount = isset($data['amount']) ? (float) $data['amount'] : 0;
            $isFree = false;
            if ($data['type'] === 'event') {
                $isFree = $amount <= 0;
            } else {
                $canPaid = $request->boolean('canPaid');
                $isFree = ! $canPaid || $amount <= 0;
            }
            if (! $isFree && ! Subscription::hasActiveSubscription($user->id, SubscriptionPlan::TYPE_CREATE_ACTIVITIES)) {
                session(['url.intended' => route('campaigns.edit', $uuid)]);

                return redirect()->route('subscriptions.checkout', ['plan' => 'create_activities'])
                    ->with('error', 'Pour publier une activité payante, abonnez-vous au plan « Création d\'activités ».');
            }
        }

        $newFilePath = null;
        if ($request->hasFile('file')) {
            if (! empty($campaign->file)) {
                Storage::disk('public')->delete($campaign->file);
            }
            $newFilePath = $request->file('file')->store('feed/', 'public');
        }

        $currentIsTraining = $feed->feedable_type === Training::class;
        $targetIsTraining = $data['type'] === 'training';

        if ($currentIsTraining !== $targetIsTraining) {
            $oldCampaign = $campaign;
            $oldMorphType = $feed->feedable_type;
            $categoryIds = $oldCampaign->categories->pluck('id')->all();

            DB::table('categorizable')
                ->where('categorizable_id', $oldCampaign->id)
                ->where('categorizable_type', $oldMorphType)
                ->delete();

            $commonFile = $newFilePath ?? $oldCampaign->file;

            if ($targetIsTraining) {
                $newModel = Training::create([
                    'title' => $data['title'],
                    'description' => $data['description'] ?? null,
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'] ?? null,
                    'amount' => $data['amount'] ?? 0,
                    'location' => $data['location'] ?? null,
                    'place' => $data['place'] ?? null,
                    'link' => $data['link'] ?? null,
                    'canPaid' => $request->boolean('canPaid'),
                    'file' => $commonFile,
                ]);
            } else {
                $newModel = Event::create([
                    'title' => $data['title'],
                    'description' => $data['description'] ?? null,
                    'start_date' => $data['start_date'],
                    'amount' => $data['amount'] ?? 0,
                    'location' => $data['location'] ?? null,
                    'file' => $commonFile,
                ]);
            }

            if (! empty($categoryIds)) {
                $newModel->attachCategories($categoryIds);
            }

            $feed->feedable_id = $newModel->id;
            $feed->feedable_type = $targetIsTraining ? Training::class : Event::class;
            $feed->status = $status;
            $feed->save();

            Registration::where('feed_id', $feed->id)->update([
                'feed_type' => $feed->feedable_type,
            ]);

            $oldCampaign->delete();

            CacheService::clearFeedsCache();
            CacheService::clearUserCache($feed->user_id);
            CacheService::forget('feed_'.$feed->id);

            return redirect()->route('dashboard.campaigns')->with('success', 'Campagne modifiée (type mis à jour).');
        }

        if ($request->hasFile('file')) {
            $data['file'] = $newFilePath;
        } else {
            unset($data['file']);
        }

        if ($feed->feedable_type === Event::class) {
            $campaign->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'start_date' => $data['start_date'],
                'amount' => $data['amount'] ?? 0,
                'location' => $data['location'] ?? null,
                'file' => $data['file'] ?? $campaign->file,
            ]);
        } else {
            $campaign->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'] ?? null,
                'amount' => $data['amount'] ?? 0,
                'location' => $data['location'] ?? null,
                'place' => $data['place'] ?? null,
                'link' => $data['link'] ?? null,
                'canPaid' => $request->boolean('canPaid'),
                'file' => $data['file'] ?? $campaign->file,
            ]);
        }

        $feed->update(['status' => $data['status']]);

        if ($request->has('categories')) {
            DB::table('categorizable')
                ->where('categorizable_id', $campaign->id)
                ->where('categorizable_type', $feed->feedable_type)
                ->delete();

            $categoryIds = array_unique(array_filter($request->input('categories', [])));
            if (! empty($categoryIds)) {
                $campaign->attachCategories($categoryIds);
            }
        }

        CacheService::clearFeedsCache();
        CacheService::clearUserCache($feed->user_id);
        CacheService::forget('feed_'.$feed->id);

        return redirect()->route('dashboard.campaigns')->with('success', 'Campagne modifiée avec succès.');
    }

    public function campaignDestroy(Request $request, string $uuid)
    {
        $user = Auth::user();

        if (! CampaignWriteMac::validDestroy($request, $user->id, $uuid)) {
            return redirect()->route('dashboard.campaigns')
                ->with('error', 'Impossible de supprimer : session expirée ou formulaire invalide. Rechargez la page « Mes campagnes » et réessayez.');
        }

        $feed = $this->managedFeedQuery($user)
            ->with('feedable')
            ->where('id', $uuid)
            ->firstOrFail();
        $campaign = $feed->feedable;

        if ($campaign && !empty($campaign->file)) {
            Storage::disk('public')->delete($campaign->file);
        }

        DB::table('categorizable')
            ->where('categorizable_id', $campaign->id)
            ->where('categorizable_type', $feed->feedable_type)
            ->delete();

        $feed->registrations()->delete();
        $feed->delete();
        $campaign?->delete();

        CacheService::clearFeedsCache();
        CacheService::clearUserCache($feed->user_id);
        CacheService::forget('feed_'.$uuid);

        return redirect()->route('dashboard.campaigns')->with('success', 'Campagne supprimée avec succès.');
    }

    public function campaignRegistrations($uuid)
    {
        $user = Auth::user();
        
        // Récupérer la campagne avec ses inscriptions
        $feed = $this->managedFeedQuery($user)
            ->where('id', $uuid)
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

    /**
     * Exporter la liste des inscrits d'une campagne en PDF
     */
    public function exportCampaignRegistrationsPdf($uuid)
    {
        $user = Auth::user();
        
        // Récupérer la campagne avec ses inscriptions
        $feed = $this->managedFeedQuery($user)
            ->where('id', $uuid)
            ->with(['feedable', 'registrations.user'])
            ->firstOrFail();

        $activity = $feed->feedable;
        $registrations = $feed->registrations()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistiques
        $stats = [
            'total' => $registrations->count(),
            'confirmed' => $registrations->where('status', Registration::STATUS_CONFIRMED)->count(),
            'pending' => $registrations->where('status', Registration::STATUS_PENDING)->count(),
            'cancelled' => $registrations->where('status', Registration::STATUS_CANCELLED)->count(),
            'paid' => $registrations->where('payment_status', 'paid')->count(),
            'totalRevenue' => $registrations->where('payment_status', 'paid')->sum('amount_paid'),
        ];

        $data = [
            'feed' => $feed,
            'activity' => $activity,
            'registrations' => $registrations,
            'stats' => $stats,
            'creator' => $user,
            'generatedAt' => now(),
        ];

        $pdf = \PDF::loadView('campagnes.exports.registrations-pdf', $data);
        
        // Nom du fichier
        $filename = 'inscrits_' . \Str::slug($activity->title) . '_' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
}
