<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Training;
use App\Models\Feed;
use App\Models\Category;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FeedController extends Controller
{

    public function index(Request $request)
    {
        // Si on demande "Gratuites", il faut être connecté + abonné
        $showFreeOnly = $request->has('free') && $request->free === 'true';
        if ($showFreeOnly) {
            if (!Auth::check()) {
                session(['url.intended' => route('home', ['free' => 'true'])]);
                return redirect()->route('login')
                    ->with('error', 'Vous devez être connecté et avoir un abonnement actif pour accéder aux événements gratuits.');
            }

            if (!Subscription::hasActiveSubscription(Auth::id(), SubscriptionPlan::TYPE_FREE_EVENTS)) {
                session(['url.intended' => route('home', ['free' => 'true'])]);
                return redirect()->route('subscriptions.checkout', ['plan' => 'free_events'])
                    ->with('error', 'Vous devez avoir un abonnement actif pour accéder aux événements gratuits.');
            }
        }

        // Cache key basé sur les paramètres de requête - TTL 10 minutes
        $cacheKey = CacheService::feedsKey($request->only(['category', 'free', 'type', 'zone', 'date_order']));
        
        // Récupérer depuis le cache ou exécuter la requête (10 minutes)
        $data = CacheService::remember($cacheKey, function () use ($request) {
            $query = Feed::where('isPrivate', false)
                ->where('status', 'publiée')
                ->with(['feedable.categories', 'user']);

            // Filter by category in database if requested
            if ($request->filled('category')) {
                $query->whereHas('feedable.categories', function($q) use ($request) {
                    $q->where('categories.id', $request->category);
                });
            }

            // Filter by type: formation | event
            if ($request->filled('type')) {
                if ($request->type === 'formation') {
                    $query->where('feedable_type', Training::class);
                } elseif ($request->type === 'event') {
                    $query->where('feedable_type', Event::class);
                }
            }

            // Filter by zone (ville)
            if ($request->filled('zone') && $request->zone !== 'all') {
                $zones = config('digitposts.zones', []);
                $zoneItem = collect($zones)->firstWhere('id', $request->zone);
                if ($zoneItem && !empty($zoneItem['name'])) {
                    $zoneName = $zoneItem['name'];
                    $query->where(function($q) use ($zoneName) {
                        $q->whereHasMorph('feedable', [Event::class], function($q) use ($zoneName) {
                            $q->where('location', 'like', '%' . $zoneName . '%');
                        })->orWhereHasMorph('feedable', [Training::class], function($q) use ($zoneName) {
                            $q->where('location', 'like', '%' . $zoneName . '%');
                        });
                    });
                }
            }

            // Filter by free activities in database if requested
            $showFreeOnly = $request->has('free') && $request->free === 'true';
            if ($showFreeOnly) {
                $query->where(function($q) {
                    $q->whereHasMorph('feedable', [Event::class], function($q) {
                        $q->where('amount', '<=', 0);
                    })->orWhereHasMorph('feedable', [Training::class], function($q) {
                        $q->where('canPaid', false);
                    });
                });
            }

            // Exclure les feeds dont la date est passée (event: start_date, formation: end_date ou start_date)
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

            // Tri par date (début d'activité) si demandé
            if ($request->filled('date_order')) {
                $asc = $request->date_order === 'proche';
                $publicFeeds = $publicFeeds->sort(function ($a, $b) use ($asc) {
                    $dateA = $a->feedable ? (\Carbon\Carbon::parse($a->feedable->start_date)->timestamp ?? 0) : 0;
                    $dateB = $b->feedable ? (\Carbon\Carbon::parse($b->feedable->start_date)->timestamp ?? 0) : 0;
                    return $asc ? $dateA <=> $dateB : $dateB <=> $dateA;
                })->values();
            }

            // Separate events and trainings feeds
            $eventFeeds = $publicFeeds->filter(function($feed) {
                return $feed->feedable_type === 'App\Models\Event';
            });
            
            $trainingFeeds = $publicFeeds->filter(function($feed) {
                return $feed->feedable_type === 'App\Models\Training';
            });

            // Count upcoming activities (optimized)
            $upcomingCount = $publicFeeds->filter(function($feed) {
                if (!$feed->feedable) return false;
                
                if ($feed->feedable_type === 'App\Models\Event') {
                    return Carbon::parse($feed->feedable->start_date)->isFuture();
                } else {
                    $date = $feed->feedable->end_date ?? $feed->feedable->start_date;
                    return Carbon::parse($date)->isFuture();
                }
            })->count();

            // Count free activities (optimized)
            $freeCount = $publicFeeds->filter(function($feed) {
                if (!$feed->feedable) return false;
                
                if ($feed->feedable_type === 'App\Models\Event') {
                    return $feed->feedable->amount <= 0;
                } else {
                    return !$feed->feedable->canPaid;
                }
            })->count();

            // Tous les feeds (événements + formations) avec image pour le swiper (sans limite, on mélange après)
            $allFeedsWithImage = $publicFeeds->filter(function($feed) {
                return $feed->feedable && !empty($feed->feedable->file);
            });

            return [
                'eventFeeds' => $eventFeeds,
                'trainingFeeds' => $trainingFeeds,
                'upcomingCount' => $upcomingCount,
                'freeCount' => $freeCount,
                'swiperFeedsPool' => $allFeedsWithImage->values()
            ];
        });

        // Ces variables dépendent de la requête, pas du cache
        $selectedCategory = $request->get('category');
        $showFreeOnly = $request->has('free') && $request->free === 'true';

        // Swiper : choisir des images au hasard dans le feed (événements + formations), max 10
        $swiperFeedsPool = $data['swiperFeedsPool'] ?? collect();
        $swiperFeeds = $swiperFeedsPool->shuffle()->take(10)->values();

        // Get all categories for filter (cached for 24 hours)
        $categories = CacheService::remember('categories_list', function () {
            return Category::distinct()->get(['id', 'name', 'type']);
        }, CacheService::TTL_DAY);

        $zones = config('digitposts.zones', []);
        $tarifsDiffusion = config('digitposts.tarifs_diffusion', []);

        // SEO Data
        $swiperEvents = $swiperFeeds; // garder pour seoImage (première image)
        $seoData = [
            'seoTitle' => 'DigitPosts - Formations & Événements au Burkina Faso',
            'seoDescription' => config('digitposts.description_short', 'Plateforme de formations et d\'événements au Burkina Faso.') . ' ' .
                               'Découvrez les formations et événements, inscrivez-vous facilement.',
            'seoKeywords' => 'formations, événements, Burkina Faso, développement professionnel, formations gratuites, événements professionnels, formations en ligne, formations certifiantes',
            'seoImage' => $swiperEvents->count() > 0 && $swiperEvents->first()->feedable && $swiperEvents->first()->feedable->file 
                ? asset('storage/' . $swiperEvents->first()->feedable->file) 
                : asset('asset/image1_large.jpg'),
            'seoUrl' => route('home'),
            'seoType' => 'website',
            'seoStructuredData' => [
                \App\Helpers\SeoHelper::organizationSchema(),
                \App\Helpers\SeoHelper::websiteSchema(),
                \App\Helpers\SeoHelper::itemListSchema($data['trainingFeeds']->take(10), 'Formations disponibles')
            ]
        ];

        return view('welcome', array_merge($data, $seoData, [
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'showFreeOnly' => $showFreeOnly,
            'zones' => $zones,
            'tarifsDiffusion' => $tarifsDiffusion,
            'selectedType' => $request->get('type'),
            'selectedZone' => $request->get('zone'),
            'selectedDateOrder' => $request->get('date_order'),
            'swiperFeeds' => $swiperFeeds,
        ]));
    }

    public function create()
    {
        return view('feeds.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:event,training',
        ]);

        if ($request->type === 'event') {
            $feedable = Event::create($request->only([
                'title', 'description', 'start_date', 'file', 'amount'
            ]));
        } else {
            $feedable = Training::create($request->only([
                'title', 'description', 'file', 'start_date',
                'end_date', 'location', 'place', 'amount', 'canPaid', 'link'
            ]));
        }

        // Create the feed
        $feed = new Feed([
            'isPrivate' => $request->isPrivate ?? false,
            'status' => 'active',
            'user_id' => auth()->id()
        ]);

        $feedable->feed()->save($feed);

        // Nettoyer le cache des feeds
        CacheService::clearFeedsCache();

        return redirect()->route('feeds.index')->with('success', 'Activity created successfully');
    }

    public function show($uuid)
    {
        // Cache le feed pendant 5 minutes
        $feed = CacheService::remember('feed_' . $uuid, function () use ($uuid) {
            return Feed::where('id', $uuid)->with(['feedable.categories', 'user'])->firstOrFail();
        }, CacheService::TTL_SHORT);
        
        // SEO Data
        $feedable = $feed->feedable;
        $isEvent = $feedable instanceof \App\Models\Event;
        $typeLabel = $isEvent ? 'Événement' : 'Formation';
        
        $seoData = [
            'seoTitle' => $feedable->title . ' - ' . $typeLabel . ' | DigitPosts',
            'seoDescription' => Str::limit(strip_tags($feedable->description ?? ''), 160) ?: 
                               'Découvrez ce ' . strtolower($typeLabel) . ' sur DigitPosts. ' . 
                               ($feedable->amount > 0 ? 'Prix: ' . number_format($feedable->amount, 0, ',', ' ') . ' FCFA' : 'Gratuit'),
            'seoKeywords' => $typeLabel . ', ' . ($feedable->categories->pluck('name')->implode(', ') ?: 'formation, événement') . ', Burkina Faso',
            'seoImage' => $feedable->file ? asset('storage/' . $feedable->file) : asset('asset/image1_large.jpg'),
            'seoUrl' => route('campaigns.show', $feed->id),
            'seoType' => 'article',
            'seoPublishedTime' => $feed->created_at->toIso8601String(),
            'seoModifiedTime' => $feed->updated_at->toIso8601String(),
            'seoStructuredData' => \App\Helpers\SeoHelper::eventSchema($feed)
        ];
        
        return view('campagnes.show', array_merge(compact('feed'), $seoData));
    }

    public function edit(Feed $feed)
    {
        $this->authorize('update', $feed);

        return view('feeds.edit', [
            'feed' => $feed->load('feedable')
        ]);
    }

    public function update(Request $request, Feed $feed)
    {
        $this->authorize('update', $feed);

        // Update the feedable model
        if ($feed->feedable_type === 'App\Models\Event') {
            $feed->feedable->update($request->only([
                'title', 'description', 'start_date', 'file', 'amount'
            ]));
        } else {
            $feed->feedable->update($request->only([
                'title', 'description', 'file', 'start_date',
                'end_date', 'location', 'place', 'amount', 'canPaid', 'link'
            ]));
        }

        // Update the feed
        $feed->update([
            'isPrivate' => $request->isPrivate ?? $feed->isPrivate,
            'status' => $request->status ?? $feed->status
        ]);

        // Nettoyer le cache des feeds et du feed spécifique
        CacheService::clearFeedsCache();
        CacheService::forget('feed_' . $feed->id);

        return redirect()->route('feeds.show', $feed)->with('success', 'Activity updated successfully');
    }

    public function destroy(Feed $feed)
    {
        $this->authorize('delete', $feed);

        $feedId = $feed->id;
        $feed->feedable->delete(); // Delete the polymorphic model
        $feed->delete(); // Delete the feed

        // Nettoyer le cache des feeds et du feed spécifique
        CacheService::clearFeedsCache();
        CacheService::forget('feed_' . $feedId);

        return redirect()->route('feeds.index')->with('success', 'Activity deleted successfully');
    }

    public function feedComment(Feed $feed)
    {
        // Implement comment functionality
    }

    public function addComment(Request $request, Feed $feed)
    {
        // Implement adding comments
    }

    public function registration(Request $request, Feed $feed)
    {
        $feedable = $feed->feedable; // Training ou Event

        $registration = new Registration([
            'user_id' => Auth::id(),
            'status' => Registration::STATUS_PENDING,
            'payment_status' => $feedable->amount > 0
                ? Registration::PAYMENT_PENDING
                : Registration::PAYMENT_PAID,
            'notes' => $request->notes,
        ]);

        $feed->registrations()->save($registration);

        return redirect()->back()
            ->with('success', 'Inscription enregistrée avec succès!');
    }
}
