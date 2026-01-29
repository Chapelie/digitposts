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

        $subscription = Subscription::createFromPlan($user->id, $plan);

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

            if ($subscription->payment_transaction_id) {
                $checkResponse = $this->cinetPayService->checkPaymentStatus($subscription->payment_transaction_id);

                if ($checkResponse['success'] && isset($checkResponse['data']['status']) && $checkResponse['data']['status'] === 'ACCEPTED') {
                    $subscription->markAsPaid(
                        $subscription->payment_transaction_id,
                        $checkResponse['data']
                    );

                    $msg = $subscription->plan_type === SubscriptionPlan::TYPE_FREE_EVENTS
                        ? 'Abonnement activé ! Vous avez accès aux événements gratuits.'
                        : 'Abonnement activé ! Vous pouvez créer et publier des activités.';

                    return redirect()->route('subscriptions.index')->with('success', $msg);
                }
            }

            return redirect()->route('subscriptions.index')
                ->with('error', 'Le paiement n\'a pas été confirmé. Veuillez réessayer.');

        } catch (\Exception $e) {
            Log::error('Erreur lors du retour de paiement d\'abonnement: ' . $e->getMessage());
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

            // Vérifier le statut du paiement
            $checkResponse = $this->cinetPayService->checkPaymentStatus($transactionId);

            if ($checkResponse['success'] && isset($checkResponse['data']['status']) && $checkResponse['data']['status'] === 'ACCEPTED') {
                $subscription->markAsPaid(
                    $transactionId,
                    $checkResponse['data']
                );

                Log::info('Abonnement marqué comme payé', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                ]);

                return response()->json(['success' => true, 'message' => 'Abonnement activé']);
            }

            return response()->json(['success' => false, 'message' => 'Paiement non confirmé']);

        } catch (\Exception $e) {
            Log::error('Erreur lors du traitement de la notification d\'abonnement: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }
}
