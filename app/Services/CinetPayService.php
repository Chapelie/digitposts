<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * CinetPay API v1 : OAuth + POST /v1/payment + GET /v1/payment/{token}
 *
 * @see https://api.cinetpay.net (sandbox)
 */
class CinetPayService
{
    protected string $baseUrl;

    protected string $apiKey;

    protected string $apiPassword;

    public function __construct()
    {
        $this->baseUrl = config('cinetpay.base_url', 'https://api.cinetpay.net');
        $this->apiKey = (string) config('cinetpay.api_key', '');
        $this->apiPassword = (string) config('cinetpay.api_password', '');
    }

    protected function ensureCredentials(): void
    {
        if ($this->apiKey === '' || $this->apiPassword === '') {
            throw new \RuntimeException(
                'CinetPay API v1 : définissez CINETPAY_API_KEY et CINETPAY_API_PASSWORD dans .env.'
            );
        }
    }

    /**
     * Jeton Bearer (mis en cache jusqu’à expiration).
     */
    public function getAccessToken(bool $forceRefresh = false): string
    {
        $this->ensureCredentials();

        if ($forceRefresh) {
            Cache::forget('cinetpay_v1_access_token');
        }

        $cached = Cache::get('cinetpay_v1_access_token');
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $response = Http::asJson()
            ->timeout(30)
            ->post($this->baseUrl . '/v1/oauth/login', [
                'api_key' => $this->apiKey,
                'api_password' => $this->apiPassword,
            ]);

        $body = $response->json() ?? [];

        if (!$response->successful() || (int) ($body['code'] ?? 0) !== 200) {
            Log::error('CinetPay OAuth échec', ['body' => $body, 'status' => $response->status()]);
            throw new \RuntimeException($body['message'] ?? 'Échec authentification CinetPay (OAuth).');
        }

        $token = $body['access_token'] ?? '';
        if ($token === '') {
            throw new \RuntimeException('CinetPay : access_token manquant.');
        }

        $ttl = max(60, (int) ($body['expires_in'] ?? 3600) - 120);
        Cache::put('cinetpay_v1_access_token', $token, $ttl);

        return $token;
    }

    /**
     * Initialise un paiement web → payment_url (redirection client).
     */
    public function createPayment(array $data): array
    {
        try {
            $merchantId = $this->normalizeMerchantTransactionId(
                $data['transaction_id'] ?? $data['merchant_transaction_id'] ?? null
            );

            $amount = (int) $data['amount'];
            $currency = strtoupper($data['currency'] ?? 'XOF');
            if ($currency !== 'USD' && $amount % 5 !== 0) {
                $amount = (int) ceil($amount / 5) * 5;
            }

            $first = $this->clientName($data['customer_name'] ?? $data['client_first_name'] ?? 'Client', true);
            $last = $this->clientName($data['customer_surname'] ?? $data['client_last_name'] ?? 'Utilisateur', false);

            $successUrl = $this->truncateUrl($data['success_url'] ?? $data['return_url'] ?? url('/'));
            $failedUrl = $this->truncateUrl($data['failed_url'] ?? $data['cancel_url'] ?? $successUrl);
            $notifyUrl = $this->truncateUrl($data['notify_url'] ?? url('/payments/notify'));

            $lang = $data['lang'] ?? 'fr';
            if (! in_array($lang, ['fr', 'en'], true)) {
                $lang = 'fr';
            }

            $payload = [
                'currency' => $currency,
                'merchant_transaction_id' => $merchantId,
                'amount' => $amount,
                'lang' => $lang,
                'designation' => $this->sanitizeDesignation($data['description'] ?? $data['designation'] ?? 'Paiement'),
                'client_email' => $data['customer_email'] ?? $data['client_email'] ?? 'client@example.com',
                'client_first_name' => $first,
                'client_last_name' => $last,
                'success_url' => $successUrl,
                'failed_url' => $failedUrl,
                'notify_url' => $notifyUrl,
                'direct_pay' => (bool) ($data['direct_pay'] ?? false),
            ];

            $phone = $data['customer_phone_number'] ?? $data['client_phone_number'] ?? null;
            if ($phone !== null && $phone !== '') {
                $payload['client_phone_number'] = $this->normalizePhone((string) $phone);
            }

            $pm = $data['payment_method'] ?? $data['channels'] ?? null;
            if ($pm && $pm !== 'ALL' && ! in_array($pm, ['MOBILE_MONEY', 'CREDIT_CARD', 'WALLET'], true)) {
                $payload['payment_method'] = $pm;
            } elseif ($pm === 'MOBILE_MONEY') {
                // Laisser vide = toutes les méthodes mobile du pays
            }

            if (! empty($data['otp_code'])) {
                $payload['otp_code'] = $data['otp_code'];
            }

            Log::info('CinetPay v1 /payment', ['merchant_transaction_id' => $merchantId, 'amount' => $amount]);

            $token = $this->getAccessToken();
            $response = Http::withToken($token)
                ->asJson()
                ->acceptJson()
                ->timeout(45)
                ->post($this->baseUrl . '/v1/payment', $payload);

            if ($response->status() === 401) {
                $token = $this->getAccessToken(true);
                $response = Http::withToken($token)
                    ->asJson()
                    ->acceptJson()
                    ->timeout(45)
                    ->post($this->baseUrl . '/v1/payment', $payload);
            }

            $body = $response->json() ?? [];

            if (! $response->successful() || (int) ($body['code'] ?? 0) !== 200) {
                $msg = $body['message'] ?? $body['description'] ?? 'Erreur initialisation paiement';
                Log::error('CinetPay v1 payment erreur', ['body' => $body]);

                return [
                    'success' => false,
                    'message' => is_string($msg) ? $msg : json_encode($msg),
                    'code' => $body['code'] ?? null,
                ];
            }

            $paymentUrl = $body['payment_url'] ?? '';
            if ($paymentUrl === '') {
                return ['success' => false, 'message' => 'payment_url manquant dans la réponse CinetPay.'];
            }

            return [
                'success' => true,
                'payment_url' => $paymentUrl,
                'payment_token' => $body['payment_token'] ?? null,
                'notify_token' => $body['notify_token'] ?? null,
                'cinetpay_transaction_id' => $body['transaction_id'] ?? null,
                'merchant_transaction_id' => $body['merchant_transaction_id'] ?? $merchantId,
                'transaction_id' => $merchantId,
                'data' => $body,
            ];
        } catch (\Throwable $e) {
            Log::error('CinetPay createPayment exception', ['e' => $e->getMessage()]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Statut d’un paiement (payment_token retourné à l’init).
     */
    public function checkPaymentStatus(string $paymentToken): array
    {
        if ($paymentToken === '') {
            return ['success' => false, 'message' => 'payment_token vide'];
        }

        try {
            $token = $this->getAccessToken();
            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(30)
                ->get($this->baseUrl . '/v1/payment/' . rawurlencode($paymentToken));

            if ($response->status() === 401) {
                $token = $this->getAccessToken(true);
                $response = Http::withToken($token)
                    ->acceptJson()
                    ->timeout(30)
                    ->get($this->baseUrl . '/v1/payment/' . rawurlencode($paymentToken));
            }

            $body = $response->json() ?? [];
            $code = (int) ($body['code'] ?? 0);
            $status = strtoupper((string) ($body['status'] ?? ''));

            if ($code === 100 && $status === 'SUCCESS') {
                return [
                    'success' => true,
                    'data' => [
                        'status' => 'ACCEPTED',
                        'amount' => 0,
                        'currency' => 'XOF',
                        'payment_method' => null,
                        'payment_date' => now()->toDateTimeString(),
                        'transaction_id' => $body['merchant_transaction_id'] ?? null,
                        'cinetpay_transaction_id' => $body['transaction_id'] ?? null,
                        'user' => $body['user'] ?? null,
                        'raw' => $body,
                    ],
                ];
            }

            return [
                'success' => false,
                'message' => $body['message'] ?? 'Paiement non confirmé',
                'data' => [
                    'status' => $status !== '' ? $status : 'UNKNOWN',
                    'raw' => $body,
                ],
            ];
        } catch (\Throwable $e) {
            Log::error('CinetPay checkPaymentStatus', ['e' => $e->getMessage()]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Vérifie le notify_token reçu sur le webhook v1.
     */
    public function verifyNotifyToken(?string $received, ?string $stored): bool
    {
        if ($received === null || $received === '' || $stored === null || $stored === '') {
            return false;
        }

        return hash_equals((string) $stored, (string) $received);
    }

    /**
     * Ancienne vérif HMAC (checkout v2) — conservée pour compatibilité si ancien webhook.
     */
    public function verifyHmacToken(array $requestData, string $receivedToken): bool
    {
        $secretKey = config('cinetpay.secret_key');
        if (empty($secretKey)) {
            return false;
        }

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

        $data = $cpm_site_id . $cpm_trans_id . $cpm_trans_date . $cpm_amount . $cpm_currency
            . $signature . $payment_method . $cel_phone_num . $cpm_phone_prefixe
            . $cpm_language . $cpm_version . $cpm_payment_config . $cpm_page_action
            . $cpm_custom . $cpm_designation . $cpm_error_message;

        $generated = hash_hmac('SHA256', $data, $secretKey);

        return hash_equals($generated, $receivedToken);
    }

    public function processNotification(array $requestData): array
    {
        $tid = $requestData['cpm_trans_id'] ?? null;
        if ($tid) {
            return ['success' => false, 'message' => 'Utiliser le webhook API v1 (JSON)'];
        }

        return ['success' => false, 'message' => 'Format inconnu'];
    }

    protected function normalizeMerchantTransactionId(?string $id): string
    {
        if ($id === null || $id === '') {
            $id = 'P' . substr(str_replace('-', '', (string) Str::uuid()), 0, 20);
        }
        $id = preg_replace('/[^a-zA-Z0-9]/', '', $id) ?: 'P' . time();

        return substr($id, 0, 30);
    }

    protected function clientName(string $name, bool $isFirst): string
    {
        $name = trim($name) ?: ($isFirst ? 'Client' : 'User');
        if (mb_strlen($name) < 2) {
            $name = $isFirst ? 'Client' : 'User';
        }

        return mb_substr($name, 0, 255);
    }

    protected function sanitizeDesignation(string $s): string
    {
        $s = preg_replace('/[#\/\$_&]/', ' ', $s);

        return mb_substr(trim($s) ?: 'Paiement', 0, 500);
    }

    protected function truncateUrl(string $url): string
    {
        $url = trim($url);
        if (strlen($url) <= 120) {
            return $url;
        }

        return substr($url, 0, 120);
    }

    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', $phone);
        if ($phone !== '' && $phone[0] !== '+') {
            $phone = '+' . ltrim($phone, '0');
        }

        return $phone;
    }
}
