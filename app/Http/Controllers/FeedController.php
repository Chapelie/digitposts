<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Training;
use App\Models\Feed;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FeedController extends Controller
{

    public function index(Request $request)
    {
        // Cache key basé sur les paramètres de requête
        $cacheKey = 'feeds_index_' . md5(json_encode($request->only(['category', 'free'])));
        
        // Récupérer depuis le cache ou exécuter la requête
        $data = Cache::remember($cacheKey, 300, function () use ($request) {
            $query = Feed::where('isPrivate', false)
                ->where('status', 'publiée')
                ->with(['feedable.categories', 'user']);

            // Filter by category in database if requested
            $selectedCategory = null;
            if ($request->has('category') && $request->category) {
                $selectedCategory = $request->category;
                $query->whereHas('feedable.categories', function($q) use ($selectedCategory) {
                    $q->where('categories.id', $selectedCategory);
                });
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

            $publicFeeds = $query->latest()->get();

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

            // Récupérer les événements avec images pour le swiper (max 10)
            $swiperEvents = $eventFeeds->filter(function($feed) {
                return $feed->feedable && $feed->feedable->file;
            })->take(10);

            return [
                'eventFeeds' => $eventFeeds,
                'trainingFeeds' => $trainingFeeds,
                'upcomingCount' => $upcomingCount,
                'freeCount' => $freeCount,
                'selectedCategory' => $selectedCategory,
                'showFreeOnly' => $showFreeOnly,
                'swiperEvents' => $swiperEvents
            ];
        });

        // Get all categories for filter (cached)
        $categories = Cache::remember('categories_list', 3600, function () {
            return Category::distinct()->get(['id', 'name', 'type']);
        });

        // SEO Data
        $swiperEvents = $data['swiperEvents'] ?? collect();
        $seoData = [
            'seoTitle' => 'DigitPosts - Formations & Événements Professionnels au Burkina Faso',
            'seoDescription' => 'Découvrez les meilleures formations et événements professionnels au Burkina Faso. ' . 
                               'Inscrivez-vous facilement à des formations gratuites et payantes. ' . 
                               'Développez vos compétences avec DigitPosts.',
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
            'categories' => $categories
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

        // Nettoyer le cache
        Cache::flush(); // Ou plus spécifique : Cache::tags(['feeds'])->flush();

        return redirect()->route('feeds.index')->with('success', 'Activity created successfully');
    }

    public function show($uuid)
    {
        $feed = Feed::where('id', $uuid)->with(['feedable.categories', 'user'])->firstOrFail();
        
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

        // Nettoyer le cache
        Cache::forget('feeds_index_' . md5(json_encode([])));
        Cache::forget('feeds_index_' . md5(json_encode(['category' => null, 'free' => null])));

        return redirect()->route('feeds.show', $feed)->with('success', 'Activity updated successfully');
    }

    public function destroy(Feed $feed)
    {
        $this->authorize('delete', $feed);

        $feed->feedable->delete(); // Delete the polymorphic model
        $feed->delete(); // Delete the feed

        // Nettoyer le cache
        Cache::forget('feeds_index_' . md5(json_encode([])));

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
