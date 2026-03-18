<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\CinetPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            $url = session('url.intended', route('subscriptions.index'));
            session()->forget('url.intended');
            return redirect($url)
                ->with('info', 'Vous avez déjà un abonnement actif pour ce plan jusqu\'au ' . $active->end_date->format('d/m/Y'));
        }

        // Si demande de vérification du statut (AJAX)
        if ($request->has('check_status') && $request->has('subscription_id')) {
            $subscription = Subscription::where('id', $request->subscription_id)
                ->where('user_id', $user->id)
                ->first();
            
            $pt = $subscription->payment_details['payment_token'] ?? null;
            if ($subscription && $pt) {
                $checkResponse = $this->cinetPayService->checkPaymentStatus($pt);
                
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

            $merchantId = 'S' . substr(str_replace('-', '', $subscription->id), 0, 18) . substr((string) time(), -8);
            $merchantId = substr(preg_replace('/[^a-zA-Z0-9]/', '', $merchantId), 0, 30);

            $description = $subscription->plan_type === SubscriptionPlan::TYPE_FREE_EVENTS
                ? 'Abonnement acces evenements gratuits'
                : 'Abonnement creation activites';

            $baseUrl = rtrim(config('cinetpay.payment_base_url') ?? config('app.url'), '/');
            $successUrl = $baseUrl . '/' . ltrim(route('subscriptions.return', $subscription->id, false), '/');
            $failedUrl = $baseUrl . '/' . ltrim(route('subscriptions.checkout', ['plan' => $subscription->plan_type, 'failed' => '1'], false), '/');

            $paymentData = [
                'transaction_id' => $merchantId,
                'amount' => (int) round((float) $subscription->amount),
                'currency' => 'XOF',
                'description' => $description,
                'success_url' => $successUrl,
                'failed_url' => $failedUrl,
                'notify_url' => $baseUrl . '/' . ltrim(route('subscriptions.notify', [], false), '/'),
                'customer_name' => Auth::user()->firstname ?? 'Client',
                'customer_surname' => Auth::user()->lastname ?? 'User',
                'customer_email' => Auth::user()->email,
                'customer_phone_number' => Auth::user()->phone ?? null,
                'payment_method' => $request->input('payment_method'),
            ];

            $paymentResponse = $this->cinetPayService->createPayment($paymentData);

            if (! $paymentResponse['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $paymentResponse['message'] ?? 'Erreur lors de la création du paiement.',
                ], 400);
            }

            $subscription->update([
                'payment_transaction_id' => $paymentResponse['merchant_transaction_id'] ?? $merchantId,
                'payment_url' => $paymentResponse['payment_url'],
                'payment_details' => array_merge($subscription->payment_details ?? [], [
                    'payment_token' => $paymentResponse['payment_token'],
                    'notify_token' => $paymentResponse['notify_token'],
                    'cinetpay_transaction_id' => $paymentResponse['cinetpay_transaction_id'],
                    'init' => $paymentResponse['data'] ?? [],
                ]),
            ]);

            return response()->json([
                'success' => true,
                'payment_url' => $paymentResponse['payment_url'],
                'message' => 'Paiement initialisé avec succès.',
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

            // Si déjà payé, rediriger vers la tâche voulue avant l'abonnement
            if ($subscription->payment_status === 'paid') {
                $msg = $subscription->plan_type === SubscriptionPlan::TYPE_FREE_EVENTS
                    ? 'Abonnement activé ! Vous avez accès aux événements gratuits.'
                    : 'Abonnement activé ! Vous pouvez créer et publier des activités.';
                $url = session('url.intended', route('subscriptions.index'));
                session()->forget('url.intended');
                return redirect($url)->with('success', $msg);
            }

            $pt = $subscription->payment_details['payment_token'] ?? null;
            if ($pt) {
                $checkResponse = $this->cinetPayService->checkPaymentStatus($pt);

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

                        $url = session('url.intended', route('subscriptions.index'));
                        session()->forget('url.intended');
                        return redirect($url)->with('success', $msg);
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
            $payload = $request->json()->all() ?: $request->all();
            Log::info('Notification abonnement CinetPay', $payload);

            if ($request->isJson() || $request->has('notify_token') || $request->has('merchant_transaction_id')) {
                $notifyToken = $payload['notify_token'] ?? null;
                $merchantId = $payload['merchant_transaction_id'] ?? null;
                if (! $merchantId) {
                    return response()->json(['error' => 'merchant_transaction_id manquant'], 400);
                }

                $subscription = Subscription::where('payment_transaction_id', $merchantId)->first();
                if (! $subscription) {
                    return response()->json(['error' => 'not_found'], 404);
                }
                if ($subscription->payment_status === 'paid') {
                    return response()->json(['ok' => true]);
                }

                $stored = $subscription->payment_details['notify_token'] ?? null;
                if ($stored && $notifyToken && ! $this->cinetPayService->verifyNotifyToken($notifyToken, $stored)) {
                    return response()->json(['error' => 'invalid_notify_token'], 403);
                }

                $pt = $subscription->payment_details['payment_token'] ?? null;
                if (! $pt) {
                    return response()->json(['error' => 'no_payment_token'], 400);
                }

                $checkResponse = $this->cinetPayService->checkPaymentStatus($pt);
                if ($checkResponse['success'] && ($checkResponse['data']['status'] ?? '') === 'ACCEPTED') {
                    $subscription->refresh();
                    if ($subscription->payment_status !== 'paid') {
                        $subscription->markAsPaid(
                            $subscription->payment_transaction_id,
                            $checkResponse['data']
                        );
                    }
                }

                return response()->json(['ok' => true]);
            }

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            Log::error('Notification abonnement: ' . $e->getMessage());

            return response()->json(['error' => 'server'], 500);
        }
    }
}
