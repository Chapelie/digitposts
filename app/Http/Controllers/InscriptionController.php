<?php

namespace App\Http\Controllers;

use App\Jobs\SendRegistrationNotificationJob;
use App\Models\Feed;
use App\Models\Registration;
use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\CacheService;

class InscriptionController extends Controller
{
    public function create($uuid)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            // Sauvegarder l'URL de destination pour la redirection après connexion
            session(['url.intended' => route('inscriptions.create', $uuid)]);
            return redirect()->route('login')
                ->with('error', 'Vous devez être connecté pour vous inscrire à cette activité.');
        }

        // Eager loading complet pour éviter N+1
        $feed = CacheService::remember('feed_' . $uuid, function () use ($uuid) {
            return Feed::where('id', $uuid)
                ->with(['feedable.categories', 'user', 'registrations'])
                ->firstOrFail();
        }, CacheService::TTL_SHORT);
        
        // Vérifier si l'utilisateur est déjà inscrit (optimisé avec index)
        $existingRegistration = Registration::where('user_id', Auth::id())
            ->where('feed_id', $feed->id)
            ->first();
            
        if ($existingRegistration) {
            return redirect()->route('campaigns.show', $feed->id)
                ->with('error', 'Vous êtes déjà inscrit à cette activité.');
        }

        // Vérifier si l'activité est gratuite et si l'utilisateur a un abonnement actif
        $feedable = $feed->feedable;
        $isFree = false;
        
        if ($feedable instanceof \App\Models\Event) {
            $isFree = $feedable->amount <= 0;
        } elseif ($feedable instanceof \App\Models\Training) {
            $isFree = !$feedable->canPaid || $feedable->amount <= 0;
        }

        // Si l'activité est gratuite, vérifier l'abonnement "accès événements gratuits"
        if ($isFree) {
            if (!Subscription::hasActiveSubscription(Auth::id(), SubscriptionPlan::TYPE_FREE_EVENTS)) {
                return redirect()->route('subscriptions.checkout', ['plan' => 'free_events'])
                    ->with('error', 'Vous devez avoir un abonnement actif pour accéder aux événements gratuits.');
            }
        }

        // Récupérer les informations de l'utilisateur connecté
        $user = Auth::user();
        $userData = [
            'name' => $user->firstname . ' ' . $user->lastname,
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'organization' => $user->organization ?? '',
        ];

        return view('inscriptions.create', compact('feed', 'userData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'feed_id' => 'required|exists:feeds,id',
            'feed_type' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'organization' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'platform_registration' => 'nullable|boolean',
            'payment_method' => 'required_if:is_paid,true|string|in:mobile_money,card,cash',
        ]);

        try {
            DB::beginTransaction();

            $feed = Feed::findOrFail($request->feed_id);
            $feedable = $feed->feedable;
            $user = Auth::user();

            // Vérifier si l'utilisateur est déjà inscrit
            $existingRegistration = Registration::where('user_id', $user->id)
                ->where('feed_id', $feed->id)
                ->first();
                
            if ($existingRegistration) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vous êtes déjà inscrit à cette activité.'
                    ], 400);
                }
                return redirect()->route('campaigns.show', $feed->id)
                    ->with('error', 'Vous êtes déjà inscrit à cette activité.');
            }

            // Vérifier si l'activité est gratuite et si l'utilisateur a un abonnement actif
            $isFree = false;
            if ($feedable instanceof \App\Models\Event) {
                $isFree = $feedable->amount <= 0;
            } elseif ($feedable instanceof \App\Models\Training) {
                $isFree = !$feedable->canPaid || $feedable->amount <= 0;
            }

            if ($isFree && !Subscription::hasActiveSubscription($user->id, SubscriptionPlan::TYPE_FREE_EVENTS)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vous devez avoir un abonnement actif pour accéder aux événements gratuits.',
                        'redirect' => route('subscriptions.checkout', ['plan' => 'free_events']),
                    ], 403);
                }
                return redirect()->route('subscriptions.checkout', ['plan' => 'free_events'])
                    ->with('error', 'Vous devez avoir un abonnement actif pour accéder aux événements gratuits.');
            }

            // Utiliser les informations de l'utilisateur connecté si les champs sont vides
            $name = $request->name ?: $user->firstname . ' ' . $user->lastname;
            $email = $request->email ?: $user->email;
            $phone = $request->phone ?: $user->phone;
            $organization = $request->organization ?: $user->organization;

            // Créer l'inscription
            $registration = new Registration([
                'user_id' => $user->id,
                'feed_id' => $feed->id,
                'feed_type' => $request->feed_type,
                'status' => Registration::STATUS_PENDING,
                'payment_status' => $feedable->is_free ? Registration::PAYMENT_PAID : Registration::PAYMENT_PENDING,
                'amount_paid' => $feedable->is_free ? 0 : $feedable->amount,
                'notes' => $request->notes,
                'platform_registration' => $request->has('platform_registration'),
                'payment_method' => $request->payment_method ?? null,
                'registration_data' => [
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'organization' => $organization,
                ]
            ]);

            $registration->save();

            // Si c'est gratuit, confirmer automatiquement
            if ($feedable->is_free) {
                $registration->update([
                    'status' => Registration::STATUS_CONFIRMED,
                    'payment_status' => Registration::PAYMENT_PAID
                ]);
            } else {
                // Pour les activités payantes, rediriger vers le paiement
                $registration->update([
                    'status' => Registration::STATUS_PENDING,
                    'payment_status' => Registration::PAYMENT_PENDING
                ]);
            }

            // Notifier le propriétaire de l'événement
            $this->notifyOwner($feed, $registration);

            // Si l'utilisateur a choisi l'inscription sur plateforme, mettre à jour son profil
            if ($request->has('platform_registration')) {
                $this->updateUserProfile($request, $user);
            }

            DB::commit();

            // Nettoyer le cache
            CacheService::clearUserCache($feed->user_id);
            CacheService::clearUserCache($user->id);
            CacheService::forget('feed_' . $feed->id);

            if ($feedable->is_free) {
                $message = 'Inscription confirmée avec succès !';
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'redirect_url' => route('campaigns.show', $feed->id)
                    ]);
                }
                return redirect()->route('campaigns.show', $feed->id)
                    ->with('success', $message);
            } else {
                // Rediriger directement vers le paiement
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Inscription enregistrée. Redirection vers le paiement...',
                        'redirect_url' => route('payments.seamless-checkout', $registration->id)
                    ]);
                }
                return redirect()->route('payments.seamless-checkout', $registration->id)
                    ->with('success', 'Inscription enregistrée. Redirection vers le paiement...');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.'
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.')
                ->withInput();
        }
    }

    private function notifyOwner($feed, $registration): void
    {
        $owner = $feed->user;
        $activity = $feed->feedable;
        if (!$owner || !$activity) {
            return;
        }
        SendRegistrationNotificationJob::dispatch(
            $registration,
            $owner->email,
            $activity->title
        );
    }

    private function updateUserProfile($request, $user)
    {
        // Mettre à jour les informations du profil si elles ont changé
        $updates = [];
        
        if ($user->name !== $request->name) {
            $updates['name'] = $request->name;
        }
        
        if ($user->email !== $request->email) {
            $updates['email'] = $request->email;
        }
        
        if ($request->phone && $user->phone !== $request->phone) {
            $updates['phone'] = $request->phone;
        }
        
        if (!empty($updates)) {
            $user->update($updates);
        }
    }

    public function index()
    {
        // Eager loading complet pour éviter N+1
        $registrations = Registration::where('user_id', Auth::id())
            ->with(['feed.feedable.categories', 'feed.user', 'user'])
            ->latest()
            ->paginate(10);

        return view('inscriptions.index', compact('registrations'));
    }

    public function show(Registration $registration)
    {
        // Vérifier que l'utilisateur peut voir cette inscription
        if ($registration->user_id !== Auth::id()) {
            abort(403);
        }

        // Eager loading pour la vue détail
        $registration->load(['feed.feedable.categories', 'feed.user', 'user']);

        return view('inscriptions.show', compact('registration'));
    }
} 