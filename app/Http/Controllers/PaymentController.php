<?php

namespace App\Http\Controllers;

use App\Jobs\SendReceiptEmailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Registration;
use App\Services\CinetPayService;
use App\Services\ReceiptService;

class PaymentController extends Controller
{
    protected $cinetPayService;
    protected $receiptService;

    public function __construct(CinetPayService $cinetPayService, ReceiptService $receiptService)
    {
        $this->cinetPayService = $cinetPayService;
        $this->receiptService = $receiptService;
    }

    /**
     * Afficher la page de paiement Seamless
     */
    public function seamlessCheckout($registrationId)
    {
        try {
            $registration = Registration::with(['user', 'feed.feedable'])
                ->where('id', $registrationId)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            // Vérifier si le paiement n'a pas déjà été effectué
            if ($registration->payment_status === 'paid') {
                return redirect()->route('user.registrations')
                    ->with('warning', 'Cette inscription a déjà été payée.');
            }

            return view('payments.seamless-checkout', compact('registration'));

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage du checkout: ' . $e->getMessage());
            return redirect()->route('user.registrations')
                ->with('error', 'Erreur lors du chargement de la page de paiement.');
        }
    }

    /**
     * Initialise le paiement via l’API CinetPay v1 et renvoie payment_url (redirection).
     */
    public function initiatePayment(Request $request)
    {
        try {
            $request->validate([
                'registration_id' => 'required|uuid|exists:registrations,id',
                'amount' => 'required|numeric|min:100',
                'payment_method' => 'nullable|string|in:MOBILE_MONEY,CREDIT_CARD,WALLET,ALL',
            ]);

            $registration = Registration::with(['user', 'feed.feedable'])
                ->where('id', $request->registration_id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $feedable = $registration->feed->feedable;
            $expectedAmount = (int) ($feedable->amount ?? 0);

            if ($expectedAmount != (int) $request->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le montant ne correspond pas à l\'activité.',
                ], 400);
            }

            $base = rtrim(config('cinetpay.payment_base_url') ?? config('app.url'), '/');
            $successUrl = $base . '/' . ltrim(route('payments.return', $registration->id, false), '/');
            $failedUrl = $base . '/' . ltrim(route('payments.seamless-checkout', $registration->id, false), '/') . '?payment=failed';
            $notifyUrl = $base . '/' . ltrim(route('payments.notify', [], false), '/');

            $merchantId = 'R' . substr(str_replace('-', '', $registration->id), 0, 20) . substr((string) time(), -6);
            $merchantId = substr(preg_replace('/[^a-zA-Z0-9]/', '', $merchantId), 0, 30);

            $paymentData = [
                'transaction_id' => $merchantId,
                'amount' => $expectedAmount,
                'currency' => 'XOF',
                'description' => 'Inscription ' . ($feedable->title ?? 'activite'),
                'success_url' => $successUrl,
                'failed_url' => $failedUrl,
                'notify_url' => $notifyUrl,
                'customer_name' => $registration->user->firstname ?? 'Client',
                'customer_surname' => $registration->user->lastname ?? 'User',
                'customer_email' => $registration->user->email,
                'customer_phone_number' => $registration->user->phone ?? null,
                'payment_method' => $request->input('payment_method'),
            ];

            $result = $this->cinetPayService->createPayment($paymentData);

            if (! $result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Erreur CinetPay.',
                ], 400);
            }

            $registration->update([
                'payment_transaction_id' => $result['merchant_transaction_id'] ?? $merchantId,
                'payment_url' => $result['payment_url'],
                'payment_method' => 'online',
                'payment_details' => array_merge($registration->payment_details ?? [], [
                    'payment_token' => $result['payment_token'],
                    'notify_token' => $result['notify_token'],
                    'cinetpay_transaction_id' => $result['cinetpay_transaction_id'],
                ]),
            ]);

            return response()->json([
                'success' => true,
                'payment_url' => $result['payment_url'],
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'initialisation du paiement: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'initialisation du paiement.',
            ], 500);
        }
    }

    /**
     * Traiter les notifications de paiement (webhook CinetPay)
     * CinetPay envoie les webhooks en POST avec cpm_trans_id et autres paramètres
     * Le token HMAC est vérifié dans l'en-tête x-token pour sécuriser la requête
     */
    public function handleNotification(Request $request)
    {
        try {
            $payload = $request->json()->all() ?: $request->all();
            Log::info('Notification CinetPay', $payload);

            // API v1 : JSON { notify_token, merchant_transaction_id, transaction_id, user? }
            if ($request->isJson() || $request->has('notify_token')) {
                $notifyToken = $payload['notify_token'] ?? null;
                $merchantId = $payload['merchant_transaction_id'] ?? null;

                if (! $merchantId) {
                    return response()->json(['error' => 'merchant_transaction_id manquant'], 400);
                }

                $registration = Registration::where('payment_transaction_id', $merchantId)->first();
                if (! $registration) {
                    Log::warning('Inscription introuvable pour merchant_transaction_id', ['id' => $merchantId]);

                    return response()->json(['error' => 'not_found'], 404);
                }

                if ($registration->payment_status === 'paid') {
                    return response()->json(['ok' => true], 200);
                }

                $storedNotify = $registration->payment_details['notify_token'] ?? null;
                if ($storedNotify && $notifyToken && ! $this->cinetPayService->verifyNotifyToken($notifyToken, $storedNotify)) {
                    Log::error('notify_token invalide', ['registration_id' => $registration->id]);

                    return response()->json(['error' => 'invalid_notify_token'], 403);
                }

                $paymentToken = $registration->payment_details['payment_token'] ?? null;
                if (! $paymentToken) {
                    return response()->json(['error' => 'no_payment_token'], 400);
                }

                $status = $this->cinetPayService->checkPaymentStatus($paymentToken);
                if ($status['success'] && ($status['data']['status'] ?? '') === 'ACCEPTED') {
                    $expectedAmount = (int) ($registration->feed->feedable->amount ?? 0);
                    $paidAmount = (int) ($status['data']['amount'] ?? 0);
                    if ($paidAmount <= 0) {
                        $paidAmount = $expectedAmount;
                    }
                    if ($paidAmount >= $expectedAmount) {
                        $registration->update([
                            'payment_status' => 'paid',
                            'payment_date' => now(),
                            'amount_paid' => $paidAmount,
                            'payment_details' => array_merge($registration->payment_details ?? [], ['webhook' => $payload, 'verify' => $status['data']]),
                            'status' => 'confirmed',
                        ]);
                        SendReceiptEmailJob::dispatch($registration->fresh());
                    }
                }

                return response()->json(['ok' => true], 200);
            }

            // Ancien format v2 (formulaire)
            $receivedToken = $request->header('x-token');
            if ($receivedToken && ! $this->cinetPayService->verifyHmacToken($request->all(), $receivedToken)) {
                return response('Token HMAC invalide', 403);
            }
            $transactionId = $request->input('cpm_trans_id');
            if (! $transactionId) {
                return response('Transaction ID manquant', 400);
            }
            $registration = Registration::where('payment_transaction_id', $transactionId)->first();
            if (! $registration || $registration->payment_status === 'paid') {
                return response('OK', 200);
            }

            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('Notification paiement: ' . $e->getMessage());

            return response('Erreur interne', 500);
        }
    }

    /**
     * Vérifier le statut d'un paiement via l'API CinetPay
     */
    public function checkPaymentStatus(Request $request)
    {
        try {
            $request->validate([
                'transaction_id' => 'required|string'
            ]);

            $registration = Registration::where('payment_transaction_id', $request->transaction_id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$registration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Inscription non trouvée',
                ], 404);
            }

            $pt = $registration->payment_details['payment_token'] ?? null;
            if (! $pt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paiement non initialisé',
                ], 400);
            }

            $status = $this->cinetPayService->checkPaymentStatus($pt);

            if ($status['success']) {
                $paymentData = $status['data'];
                
                // Mettre à jour l'inscription si le statut a changé
                if (strtoupper($paymentData['status']) === 'ACCEPTED' && $registration->payment_status !== 'paid') {
                    $registration->update([
                        'payment_status' => 'paid',
                        'payment_date' => $paymentData['payment_date'] ?? now(),
                        'amount_paid' => (int) ($paymentData['amount'] ?? 0),
                        'payment_details' => $paymentData,
                        'status' => 'confirmed'
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'payment' => [
                    'registration_id' => $registration->id,
                    'status' => $registration->payment_status,
                    'cinetpay_status' => $status['success'] ? $status['data']['status'] : null,
                    'amount' => $registration->amount_paid ?? ($status['success'] ? (int) $status['data']['amount'] : 0),
                    'payment_method' => $registration->payment_method ?? ($status['success'] ? $status['data']['payment_method'] : null),
                    'created_at' => $registration->created_at,
                    'payment_date' => $registration->payment_date
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification du statut: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification'
            ], 500);
        }
    }

    /**
     * Gérer le retour après paiement CinetPay
     */
    public function handleReturn($registrationId)
    {
        try {
            $registration = Registration::with(['user', 'feed.feedable'])
                ->where('id', $registrationId)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            // Vérifier le statut du paiement via CinetPay
            $pt = $registration->payment_details['payment_token'] ?? null;
            if ($pt) {
                $status = $this->cinetPayService->checkPaymentStatus($pt);
                
                if ($status['success']) {
                    $paymentData = $status['data'];
                    $isSuccess = strtoupper($paymentData['status'] ?? '') === 'ACCEPTED';
                    
                    if ($isSuccess) {
                        $expectedAmount = (int) ($registration->feed->feedable->amount ?? 0);
                        $paidAmount = (int) ($paymentData['amount'] ?? 0);
                        if ($paidAmount < $expectedAmount) {
                            $paidAmount = $expectedAmount;
                        }
                        if ($registration->payment_status !== 'paid') {
                            $registration->update([
                                'payment_status' => 'paid',
                                'payment_date' => $paymentData['payment_date'] ?? now(),
                                'amount_paid' => $paidAmount,
                                'payment_details' => array_merge($registration->payment_details ?? [], ['return_verify' => $paymentData]),
                                'status' => 'confirmed',
                            ]);
                            SendReceiptEmailJob::dispatch($registration->fresh());
                        }
                        if ($registration->payment_status === 'paid') {
                            $url = session('url.intended', route('user.registrations'));
                            session()->forget('url.intended');
                            return redirect($url)->with('success', 'Paiement effectué avec succès ! Votre inscription est confirmée.');
                        }
                    }
                }
            }

            // Si le paiement n'est pas encore confirmé, rediriger vers la page de paiement
            return redirect()->route('payments.seamless-checkout', $registration->id)
                ->with('info', 'Vérification du paiement en cours...');

        } catch (\Exception $e) {
            Log::error('Erreur lors du retour de paiement: ' . $e->getMessage());
            return redirect()->route('user.registrations')
                ->with('error', 'Erreur lors de la vérification du paiement.');
        }
    }

    /**
     * Redirection après paiement réussi (Seamless) vers la tâche voulue.
     */
    public function afterSuccess()
    {
        $url = session('url.intended', route('user.registrations'));
        session()->forget('url.intended');
        return redirect($url)->with('success', 'Paiement effectué avec succès ! Votre inscription est confirmée.');
    }

} 