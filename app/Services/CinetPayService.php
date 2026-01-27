<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CinetPayService
{
    protected $apiKey;
    protected $siteId;
    protected $secretKey;
    protected $apiUrl;
    protected $checkUrl;

    public function __construct()
    {
        $this->apiKey = config('cinetpay.api_key');
        $this->siteId = config('cinetpay.site_id');
        $this->secretKey = config('cinetpay.secret_key');
        $this->apiUrl = config('cinetpay.api_url', 'https://api-checkout.cinetpay.com/v2/payment');
        $this->checkUrl = config('cinetpay.check_url', 'https://api-checkout.cinetpay.com/v2/payment/check');
        
        if (empty($this->apiKey) || empty($this->siteId)) {
            throw new \Exception('CinetPay API Key et Site ID doivent être configurés dans le fichier .env');
        }
    }

    /**
     * Créer un paiement CinetPay
     */
    public function createPayment($data)
    {
        try {
            Log::info('Création de paiement CinetPay', $data);

            // Générer un transaction_id unique si non fourni
            $transactionId = $data['transaction_id'] ?? 'TXN' . time() . Str::random(8);

            // Préparer les données selon le format CinetPay
            // CinetPay exige que le montant soit un multiple de 5 (sauf pour USD)
            $amount = (int) $data['amount'];
            $currency = $data['currency'] ?? 'XOF';
            
            // Arrondir au multiple de 5 supérieur si nécessaire (sauf pour USD)
            if ($currency !== 'USD' && $amount % 5 !== 0) {
                $amount = (int) ceil($amount / 5) * 5;
                Log::info('Montant arrondi au multiple de 5', [
                    'montant_original' => $data['amount'],
                    'montant_arrondi' => $amount
                ]);
            }
            
            $paymentData = [
                'apikey' => $this->apiKey,
                'site_id' => $this->siteId,
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'currency' => $currency,
                'description' => $data['description'] ?? 'Paiement',
                'notify_url' => $data['notify_url'] ?? route('payments.notify'),
                'return_url' => $data['return_url'] ?? route('payments.return', $data['registration_id'] ?? ''),
                'channels' => $data['channels'] ?? 'ALL', // ALL, MOBILE_MONEY, CREDIT_CARD, WALLET
                'lang' => $data['lang'] ?? 'fr',
            ];

            // Ajouter les métadonnées si présentes
            if (isset($data['metadata'])) {
                if (is_string($data['metadata'])) {
                    $paymentData['metadata'] = $data['metadata'];
                } elseif (is_array($data['metadata'])) {
                    $paymentData['metadata'] = json_encode($data['metadata']);
                }
            }

            // Ajouter les informations client pour le paiement par carte bancaire
            if (isset($data['customer_name'])) {
                $paymentData['customer_name'] = $data['customer_name'];
            }
            if (isset($data['customer_surname'])) {
                $paymentData['customer_surname'] = $data['customer_surname'];
            }
            if (isset($data['customer_email'])) {
                $paymentData['customer_email'] = $data['customer_email'];
            }
            if (isset($data['customer_phone_number'])) {
                $paymentData['customer_phone_number'] = $data['customer_phone_number'];
            }
            if (isset($data['customer_address'])) {
                $paymentData['customer_address'] = $data['customer_address'];
            }
            if (isset($data['customer_city'])) {
                $paymentData['customer_city'] = $data['customer_city'];
            }
            if (isset($data['customer_country'])) {
                $paymentData['customer_country'] = $data['customer_country'];
            }
            if (isset($data['customer_state'])) {
                $paymentData['customer_state'] = $data['customer_state'];
            }
            if (isset($data['customer_zip_code'])) {
                $paymentData['customer_zip_code'] = $data['customer_zip_code'];
            }

            // Ajouter invoice_data si présent
            if (isset($data['invoice_data']) && is_array($data['invoice_data'])) {
                $paymentData['invoice_data'] = $data['invoice_data'];
            }

            // Appel à l'API CinetPay
            $response = Http::asJson()
                ->timeout(30)
                ->post($this->apiUrl, $paymentData);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['code']) && $responseData['code'] === '201') {
                Log::info('Paiement CinetPay créé avec succès', [
                    'transaction_id' => $transactionId,
                    'payment_url' => $responseData['data']['payment_url'] ?? null
                ]);

                return [
                    'success' => true,
                    'payment_url' => $responseData['data']['payment_url'] ?? null,
                    'payment_token' => $responseData['data']['payment_token'] ?? null,
                    'transaction_id' => $transactionId,
                    'data' => $responseData['data'] ?? []
                ];
            } else {
                $errorMessage = $responseData['message'] ?? 'Erreur lors de la création du paiement';
                Log::error('Erreur CinetPay - Création de paiement', [
                    'error' => $errorMessage,
                    'code' => $responseData['code'] ?? null,
                    'response' => $responseData
                ]);

                return [
                    'success' => false,
                    'message' => 'Erreur CinetPay: ' . $errorMessage,
                    'code' => $responseData['code'] ?? null
                ];
            }

        } catch (\Exception $e) {
            Log::error('Exception lors de la création du paiement CinetPay', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erreur technique: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Vérifier le statut d'un paiement
     */
    public function checkPaymentStatus($transactionId)
    {
        try {
            Log::info('Vérification du statut de paiement CinetPay', [
                'transaction_id' => $transactionId
            ]);

            $checkData = [
                'apikey' => $this->apiKey,
                'site_id' => $this->siteId,
                'transaction_id' => $transactionId,
            ];

            $response = Http::asJson()
                ->timeout(30)
                ->post($this->checkUrl, $checkData);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['code']) && $responseData['code'] === '00') {
                $data = $responseData['data'] ?? [];
                
                Log::info('Statut de paiement CinetPay vérifié', [
                    'transaction_id' => $transactionId,
                    'status' => $data['status'] ?? null
                ]);

                return [
                    'success' => true,
                    'data' => [
                        'transaction_id' => $transactionId,
                        'status' => $data['status'] ?? 'UNKNOWN', // ACCEPTED, REFUSED, PENDING
                        'amount' => (int) ($data['amount'] ?? 0), // Convertir en entier
                        'currency' => $data['currency'] ?? 'XOF',
                        'payment_method' => $data['payment_method'] ?? null,
                        'payment_date' => $data['payment_date'] ?? null,
                        'operator_id' => $data['operator_id'] ?? null,
                        'description' => $data['description'] ?? '',
                        'metadata' => $data['metadata'] ?? null,
                        'fund_availability_date' => $data['fund_availability_date'] ?? null,
                    ]
                ];
            } else {
                $errorMessage = $responseData['message'] ?? 'Erreur lors de la vérification';
                Log::error('Erreur CinetPay - Vérification', [
                    'error' => $errorMessage,
                    'code' => $responseData['code'] ?? null
                ]);

                return [
                    'success' => false,
                    'message' => 'Erreur lors de la vérification: ' . $errorMessage,
                    'code' => $responseData['code'] ?? null
                ];
            }

        } catch (\Exception $e) {
            Log::error('Exception lors de la vérification du statut CinetPay', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erreur technique: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Vérifier le token HMAC d'une notification CinetPay
     * 
     * @param array $requestData Les données de la requête POST
     * @param string $receivedToken Le token reçu dans l'en-tête x-token
     * @return bool
     */
    public function verifyHmacToken($requestData, $receivedToken)
    {
        try {
            if (empty($this->secretKey)) {
                Log::warning('Secret Key CinetPay non configurée, impossible de vérifier le token HMAC');
                return false;
            }

            // Récupérer tous les champs nécessaires pour la génération du token
            $cpm_site_id = $requestData['cpm_site_id'] ?? '';
            $cpm_trans_id = $requestData['cpm_trans_id'] ?? '';
            $cpm_trans_date = $requestData['cpm_trans_date'] ?? '';
            $cpm_amount = $requestData['cpm_amount'] ?? '';
            $cpm_currency = $requestData['cpm_currency'] ?? '';
            $signature = $requestData['signature'] ?? '';
            $payment_method = $requestData['payment_method'] ?? '';
            $cel_phone_num = $requestData['cel_phone_num'] ?? '';
            $cpm_phone_prefixe = $requestData['cpm_phone_prefixe'] ?? '';
            $cpm_language = $requestData['cpm_language'] ?? '';
            $cpm_version = $requestData['cpm_version'] ?? '';
            $cpm_payment_config = $requestData['cpm_payment_config'] ?? '';
            $cpm_page_action = $requestData['cpm_page_action'] ?? '';
            $cpm_custom = $requestData['cpm_custom'] ?? '';
            $cpm_designation = $requestData['cpm_designation'] ?? '';
            $cpm_error_message = $requestData['cpm_error_message'] ?? '';

            // Concaténer les données selon le schéma CinetPay
            $data = $cpm_site_id . $cpm_trans_id . $cpm_trans_date . $cpm_amount . $cpm_currency . 
                    $signature . $payment_method . $cel_phone_num . $cpm_phone_prefixe . 
                    $cpm_language . $cpm_version . $cpm_payment_config . $cpm_page_action . 
                    $cpm_custom . $cpm_designation . $cpm_error_message;

            // Générer le token HMAC avec SHA256
            $generatedToken = hash_hmac('SHA256', $data, $this->secretKey);

            // Comparer les tokens de manière sécurisée (timing-safe)
            $isValid = hash_equals($receivedToken, $generatedToken);

            if (!$isValid) {
                Log::warning('Token HMAC invalide', [
                    'received_token' => substr($receivedToken, 0, 20) . '...',
                    'generated_token' => substr($generatedToken, 0, 20) . '...',
                    'transaction_id' => $cpm_trans_id
                ]);
            }

            return $isValid;

        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification du token HMAC', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Traiter une notification de paiement
     */
    public function processNotification($requestData)
    {
        try {
            Log::info('Traitement de notification CinetPay', $requestData);

            $transactionId = $requestData['cpm_trans_id'] ?? null;
            
            if (!$transactionId) {
                Log::warning('Notification CinetPay sans transaction ID', $requestData);
                return [
                    'success' => false,
                    'message' => 'Transaction ID manquant'
                ];
            }

            // Vérifier le statut via l'API
            return $this->checkPaymentStatus($transactionId);

        } catch (\Exception $e) {
            Log::error('Exception lors du traitement de la notification CinetPay', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erreur technique: ' . $e->getMessage()
            ];
        }
    }
}
