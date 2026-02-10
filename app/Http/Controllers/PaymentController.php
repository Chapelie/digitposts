<?php

namespace App\Http\Controllers;

use App\Jobs\SendReceiptEmailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Registration;
use App\Services\CinetPayService;
use App\Services\ReceiptService;
use Illuminate\Support\Str;

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
     * Préparer un paiement pour le SDK Seamless (front).
     * Retourne les données pour CinetPay.getCheckout() sans appeler l’API de création.
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
                    'message' => 'Le montant ne correspond pas à l\'activité.'
                ], 400);
            }

            $transactionId = 'TXN' . time() . Str::random(8);
            $amount = $expectedAmount;
            if ($amount % 5 !== 0) {
                $amount = (int) ceil($amount / 5) * 5;
            }

            $registration->update([
                'payment_transaction_id' => $transactionId,
                'payment_method' => 'online',
            ]);

            $channels = $request->filled('payment_method') && $request->payment_method !== 'ALL'
                ? $request->payment_method
                : 'ALL';

            return response()->json([
                'success' => true,
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'currency' => 'XOF',
                'channels' => $channels,
                'description' => 'Inscription à l\'activité: ' . $feedable->title,
                'customer_name' => $registration->user->firstname ?? 'Client',
                'customer_surname' => $registration->user->lastname ?? '',
                'customer_email' => $registration->user->email,
                'customer_phone_number' => $registration->user->phone ?? '',
                'customer_address' => $registration->user->location ?? 'Non renseigné',
                'customer_city' => 'Ouagadougou',
                'customer_country' => 'BF',
                'customer_state' => 'BF',
                'customer_zip_code' => '',
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'initialisation du paiement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'initialisation du paiement.'
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
            Log::info('Notification de paiement CinetPay reçue', $request->all());

            // Récupérer le token HMAC depuis l'en-tête x-token
            $receivedToken = $request->header('x-token');
            
            if (!$receivedToken) {
                Log::warning('Notification CinetPay sans token HMAC dans l\'en-tête x-token', $request->all());
                // En mode développement, on peut continuer sans vérification
                // En production, décommenter la ligne suivante pour rejeter les requêtes sans token
                // return response('Token HMAC manquant', 400);
            } else {
                // Vérifier le token HMAC si présent
                $isValidToken = $this->cinetPayService->verifyHmacToken($request->all(), $receivedToken);
                
                if (!$isValidToken) {
                    Log::error('Token HMAC invalide - Notification rejetée', [
                        'transaction_id' => $request->input('cpm_trans_id'),
                        'ip' => $request->ip()
                    ]);
                    return response('Token HMAC invalide', 403);
                }
                
                Log::info('Token HMAC vérifié avec succès', [
                    'transaction_id' => $request->input('cpm_trans_id')
                ]);
            }

            // CinetPay envoie cpm_trans_id dans le body
            $transactionId = $request->input('cpm_trans_id');
            
            if (!$transactionId) {
                Log::warning('Notification CinetPay sans transaction ID', $request->all());
                return response('Transaction ID manquant', 400);
            }

            // Récupérer l'inscription via le transaction_id
            $registration = Registration::where('payment_transaction_id', $transactionId)->first();
            
            if (!$registration) {
                Log::warning('Inscription non trouvée pour la transaction: ' . $transactionId);
                return response('Inscription non trouvée', 404);
            }

            // Vérifier que le paiement n'a pas déjà été traité
            if ($registration->payment_status === 'paid') {
                Log::info('Paiement déjà traité', ['transaction_id' => $transactionId]);
                return response('OK', 200);
            }

            // Vérifier le statut de la transaction via l'API CinetPay
            $status = $this->cinetPayService->checkPaymentStatus($transactionId);

            Log::info('Statut de paiement vérifié', [
                'transaction_id' => $transactionId,
                'status' => $status
            ]);

            if ($status['success']) {
                $paymentData = $status['data'];
                
                // Selon la documentation CinetPay, le statut "ACCEPTED" indique un paiement réussi
                $isSuccess = strtoupper($paymentData['status'] ?? '') === 'ACCEPTED';
                
                if ($isSuccess) {
                    // Vérifier que le montant correspond
                    $expectedAmount = $registration->feed->feedable->amount ?? 0;
                    $paidAmount = (int) ($paymentData['amount'] ?? 0);
                    
                    // Le montant payé doit être égal ou supérieur au montant attendu
                    if ($paidAmount >= $expectedAmount) {
                        $registration->update([
                            'payment_status' => 'paid',
                            'payment_date' => $paymentData['payment_date'] ?? now(),
                            'amount_paid' => $paidAmount,
                            'payment_details' => $paymentData,
                            'status' => 'confirmed'
                        ]);

                        SendReceiptEmailJob::dispatch($registration->fresh());

                        Log::info('Paiement traité avec succès', [
                            'registration_id' => $registration->id,
                            'transaction_id' => $transactionId,
                            'amount_paid' => $paidAmount,
                            'expected_amount' => $expectedAmount
                        ]);
                    } else {
                        Log::warning('Montant insuffisant', [
                            'registration_id' => $registration->id,
                            'transaction_id' => $transactionId,
                            'amount_paid' => $paidAmount,
                            'expected_amount' => $expectedAmount
                        ]);
                        
                        $registration->update([
                            'payment_status' => 'failed',
                            'payment_details' => $paymentData
                        ]);
                    }
                } else {
                    // Statut non "ACCEPTED" (REFUSED, PENDING, etc.)
                    $registration->update([
                        'payment_status' => strtoupper($paymentData['status']) === 'REFUSED' ? 'failed' : 'pending',
                        'payment_details' => $paymentData
                    ]);
                    
                    Log::info('Paiement non réussi', [
                        'registration_id' => $registration->id,
                        'transaction_id' => $transactionId,
                        'status' => $paymentData['status'] ?? 'UNKNOWN'
                    ]);
                }

                return response('OK', 200);
            } else {
                Log::error('Erreur lors de la vérification du statut', $status);
                return response('Erreur de vérification', 500);
            }

        } catch (\Exception $e) {
            Log::error('Erreur lors du traitement de la notification: ' . $e->getMessage());
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
                    'message' => 'Inscription non trouvée'
                ], 404);
            }

            // Vérifier le statut via CinetPay
            $status = $this->cinetPayService->checkPaymentStatus($request->transaction_id);

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
            if ($registration->payment_transaction_id) {
                $status = $this->cinetPayService->checkPaymentStatus($registration->payment_transaction_id);
                
                if ($status['success']) {
                    $paymentData = $status['data'];
                    $isSuccess = strtoupper($paymentData['status'] ?? '') === 'ACCEPTED';
                    
                    if ($isSuccess) {
                        // Mettre à jour l'inscription si le paiement est réussi
                        $expectedAmount = $registration->feed->feedable->amount ?? 0;
                        $paidAmount = (int) ($paymentData['amount'] ?? 0);
                        
                        if ($paidAmount >= $expectedAmount && $registration->payment_status !== 'paid') {
                            $registration->update([
                                'payment_status' => 'paid',
                                'payment_date' => $paymentData['payment_date'] ?? now(),
                                'amount_paid' => $paidAmount,
                                'payment_details' => $paymentData,
                                'status' => 'confirmed'
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