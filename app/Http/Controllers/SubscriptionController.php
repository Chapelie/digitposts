<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\CinetPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    protected $cinetPayService;

    public function __construct(CinetPayService $cinetPayService)
    {
        $this->cinetPayService = $cinetPayService;
    }

    /**
     * Afficher la page d'abonnement (liste des plans + abonnements utilisateur)
     */
    public function index()
    {
        $user = Auth::user();
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('type')->get();
        $activeFreeEvents = Subscription::getActiveSubscription($user->id, SubscriptionPlan::TYPE_FREE_EVENTS);
        $activeCreateActivities = Subscription::getActiveSubscription($user->id, SubscriptionPlan::TYPE_CREATE_ACTIVITIES);
        $subscriptions = Subscription::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('subscriptions.index', compact(
            'plans',
            'activeFreeEvents',
            'activeCreateActivities',
            'subscriptions'
        ));
    }

    /**
     * Afficher la page de paiement pour un plan (free_events | create_activities)
     */
    public function checkout(Request $request)
    {
        $planType = $request->get('plan', SubscriptionPlan::TYPE_FREE_EVENTS);
        if (!in_array($planType, [SubscriptionPlan::TYPE_FREE_EVENTS, SubscriptionPlan::TYPE_CREATE_ACTIVITIES], true)) {
            return redirect()->route('subscriptions.index')->with('error', 'Type d\'abonnement invalide.');
        }

        $plan = SubscriptionPlan::getByType($planType);
        if (!$plan) {
            return redirect()->route('subscriptions.index')->with('error', 'Plan d\'abonnement indisponible.');
        }

        $user = Auth::user();
        $active = Subscription::getActiveSubscription($user->id, $planType);
        if ($active) {
            return redirect()->route('subscriptions.index')
                ->with('info', 'Vous avez déjà un abonnement actif pour ce plan jusqu\'au ' . $active->end_date->format('d/m/Y'));
        }

        // Si demande de vérification du statut (AJAX)
        if ($request->has('check_status') && $request->has('subscription_id')) {
            $subscription = Subscription::where('id', $request->subscription_id)
                ->where('user_id', $user->id)
                ->first();
            
            if ($subscription && $subscription->payment_transaction_id) {
                // Vérifier le statut via CinetPay
                $checkResponse = $this->cinetPayService->checkPaymentStatus($subscription->payment_transaction_id);
                
                if ($checkResponse['success'] && isset($checkResponse['data']['status'])) {
                    $status = strtoupper($checkResponse['data']['status'] ?? '');
                    
                    if ($status === 'ACCEPTED' && $subscription->payment_status !== 'paid') {
                        $subscription->refresh();
                        $subscription->markAsPaid(
                            $subscription->payment_transaction_id,
                            $checkResponse['data']
                        );
                        $subscription->refresh();
                    }
                }
                
                return response()->json([
                    'payment_status' => $subscription->fresh()->payment_status,
                    'status' => $checkResponse['success'] ? ($checkResponse['data']['status'] ?? 'UNKNOWN') : 'ERROR'
                ]);
            }
            
            return response()->json(['payment_status' => 'pending'], 404);
        }

        // Récupérer ou créer l'abonnement
        $subscription = Subscription::where('user_id', $user->id)
            ->where('plan_type', $planType)
            ->where('payment_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->first();
        
        if (!$subscription) {
            $subscription = Subscription::createFromPlan($user->id, $plan);
        } else {
            // Recharger pour avoir les données à jour
            $subscription->refresh();
        }

        return view('subscriptions.checkout', compact('subscription', 'plan'));
    }

    /**
     * Initialiser le paiement de l'abonnement
     */
    public function initiatePayment(Request $request)
    {
        try {
            $request->validate([
                'subscription_id' => 'required|uuid|exists:subscriptions,id',
            ]);

            $subscription = Subscription::where('id', $request->subscription_id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            if ($subscription->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cet abonnement a déjà été payé.'
                ], 400);
            }

            $transactionId = 'SUB' . time() . Str::random(8);
            $description = $subscription->plan_type === SubscriptionPlan::TYPE_FREE_EVENTS
                ? 'Abonnement - Accès aux événements gratuits'
                : 'Abonnement - Droit de créer des activités';

            $baseUrl = rtrim(config('cinetpay.payment_base_url') ?? config('app.url'), '/');
            $paymentData = [
                'transaction_id' => $transactionId,
                'amount' => (int) round((float) $subscription->amount),
                'currency' => 'XOF',
                'description' => $description,
                'subscription_id' => $subscription->id,
                'return_url' => $baseUrl . '/' . ltrim(route('subscriptions.return', $subscription->id, false), '/'),
                'notify_url' => $baseUrl . '/' . ltrim(route('subscriptions.notify', [], false), '/'),
                'customer_name' => Auth::user()->firstname,
                'customer_surname' => Auth::user()->lastname,
                'customer_email' => Auth::user()->email,
                'customer_phone_number' => Auth::user()->phone ?? null,
                'customer_address' => Auth::user()->location ?? 'Non renseigné',
                'customer_city' => 'Ouagadougou',
                'customer_country' => 'BF',
                'customer_state' => 'BF',
                'customer_zip_code' => null,
            ];

            $paymentResponse = $this->cinetPayService->createPayment($paymentData);

            if (!$paymentResponse['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $paymentResponse['message'] ?? 'Erreur lors de la création du paiement.'
                ], 400);
            }

            $subscription->update([
                'payment_transaction_id' => $transactionId,
                'payment_url' => $paymentResponse['data']['payment_url'] ?? null,
                'payment_details' => $paymentResponse['data'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'payment_url' => $paymentResponse['data']['payment_url'] ?? null,
                'message' => 'Paiement initialisé avec succès.'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'initialisation du paiement d\'abonnement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'initialisation du paiement.'
            ], 500);
        }
    }

    /**
     * Gérer le retour après paiement
     */
    public function handleReturn($subscriptionId)
    {
        try {
            $subscription = Subscription::where('id', $subscriptionId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Si déjà payé, rediriger directement
            if ($subscription->payment_status === 'paid') {
                $msg = $subscription->plan_type === SubscriptionPlan::TYPE_FREE_EVENTS
                    ? 'Abonnement activé ! Vous avez accès aux événements gratuits.'
                    : 'Abonnement activé ! Vous pouvez créer et publier des activités.';
                return redirect()->route('subscriptions.index')->with('success', $msg);
            }

            if ($subscription->payment_transaction_id) {
                $checkResponse = $this->cinetPayService->checkPaymentStatus($subscription->payment_transaction_id);

                if ($checkResponse['success'] && isset($checkResponse['data']['status'])) {
                    $status = strtoupper($checkResponse['data']['status'] ?? '');
                    
                    if ($status === 'ACCEPTED') {
                        // Recharger l'abonnement depuis la base pour éviter les conflits
                        $subscription->refresh();
                        
                        // Vérifier à nouveau le statut avant de mettre à jour
                        if ($subscription->payment_status !== 'paid') {
                            $subscription->markAsPaid(
                                $subscription->payment_transaction_id,
                                $checkResponse['data']
                            );
                            
                            // Recharger pour avoir les données à jour
                            $subscription->refresh();
                        }

                        $msg = $subscription->plan_type === SubscriptionPlan::TYPE_FREE_EVENTS
                            ? 'Abonnement activé ! Vous avez accès aux événements gratuits.'
                            : 'Abonnement activé ! Vous pouvez créer et publier des activités.';

                        return redirect()->route('subscriptions.index')->with('success', $msg);
                    } elseif ($status === 'PENDING') {
                        // Le paiement est en attente, rediriger vers la page de checkout avec un message
                        return redirect()->route('subscriptions.checkout', ['plan' => $subscription->plan_type])
                            ->with('info', 'Votre paiement est en cours de traitement. Vous recevrez une notification une fois confirmé.');
                    } else {
                        // Statut REFUSED ou autre
                        return redirect()->route('subscriptions.checkout', ['plan' => $subscription->plan_type])
                            ->with('error', 'Le paiement n\'a pas pu être confirmé. Veuillez réessayer.');
                    }
                }
            }

            return redirect()->route('subscriptions.checkout', ['plan' => $subscription->plan_type])
                ->with('error', 'Le paiement n\'a pas été confirmé. Veuillez réessayer.');

        } catch (\Exception $e) {
            Log::error('Erreur lors du retour de paiement d\'abonnement: ' . $e->getMessage(), [
                'subscription_id' => $subscriptionId,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('subscriptions.index')
                ->with('error', 'Erreur lors de la vérification du paiement.');
        }
    }

    /**
     * Gérer la notification webhook de CinetPay
     */
    public function handleNotification(Request $request)
    {
        try {
            Log::info('Notification webhook abonnement reçue', $request->all());

            // Récupérer le token HMAC depuis l'en-tête x-token (si présent)
            $receivedToken = $request->header('x-token');
            
            if ($receivedToken) {
                // Vérifier le token HMAC si présent
                $isValidToken = app(CinetPayService::class)->verifyHmacToken($request->all(), $receivedToken);
                
                if (!$isValidToken) {
                    Log::error('Token HMAC invalide - Notification abonnement rejetée', [
                        'transaction_id' => $request->input('cpm_trans_id'),
                        'ip' => $request->ip()
                    ]);
                    return response()->json(['success' => false, 'message' => 'Token HMAC invalide'], 403);
                }
            }

            $request->validate([
                'cpm_trans_id' => 'required|string',
            ]);

            $transactionId = $request->cpm_trans_id;
            
            // Chercher l'abonnement par transaction_id
            $subscription = Subscription::where('payment_transaction_id', $transactionId)
                ->first();

            if (!$subscription) {
                Log::warning('Abonnement non trouvé pour la transaction: ' . $transactionId);
                return response()->json(['success' => false, 'message' => 'Abonnement non trouvé'], 404);
            }

            // Si déjà payé, retourner OK pour éviter les doublons
            if ($subscription->payment_status === 'paid') {
                Log::info('Abonnement déjà payé', [
                    'subscription_id' => $subscription->id,
                    'transaction_id' => $transactionId
                ]);
                return response()->json(['success' => true, 'message' => 'Abonnement déjà activé']);
            }

            // Vérifier le statut du paiement
            $checkResponse = $this->cinetPayService->checkPaymentStatus($transactionId);

            if ($checkResponse['success'] && isset($checkResponse['data']['status'])) {
                $status = strtoupper($checkResponse['data']['status'] ?? '');
                
                if ($status === 'ACCEPTED') {
                    // Recharger l'abonnement pour éviter les conflits
                    $subscription->refresh();
                    
                    // Vérifier à nouveau avant de mettre à jour
                    if ($subscription->payment_status !== 'paid') {
                        $subscription->markAsPaid(
                            $transactionId,
                            $checkResponse['data']
                        );

                        Log::info('Abonnement marqué comme payé via webhook', [
                            'subscription_id' => $subscription->id,
                            'user_id' => $subscription->user_id,
                            'transaction_id' => $transactionId,
                            'amount' => $checkResponse['data']['amount'] ?? null,
                        ]);
                    }

                    return response()->json(['success' => true, 'message' => 'Abonnement activé']);
                } else {
                    Log::info('Paiement non accepté pour abonnement', [
                        'subscription_id' => $subscription->id,
                        'status' => $status
                    ]);
                }
            }

            return response()->json(['success' => false, 'message' => 'Paiement non confirmé']);

        } catch (\Exception $e) {
            Log::error('Erreur lors du traitement de la notification d\'abonnement: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }
}
