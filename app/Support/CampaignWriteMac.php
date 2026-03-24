<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Jetons HMAC pour formulaires campagne (création, édition, suppression) lorsque le CSRF session est fragile.
 */
final class CampaignWriteMac
{
    public static function appKeyBinary(): string
    {
        $k = (string) config('app.key');
        if (str_starts_with($k, 'base64:')) {
            $decoded = base64_decode(substr($k, 7), true);

            return $decoded !== false ? $decoded : $k;
        }

        return $k;
    }

    /** Création / mise à jour : HMAC(userId|timestamp) */
    public static function forStore(string|int $userId, int $timestamp): string
    {
        return hash_hmac('sha256', (string) $userId.'|'.$timestamp, self::appKeyBinary());
    }

    /** Suppression : HMAC(userId|feedId|timestamp) — une valeur par campagne */
    public static function forDestroy(string|int $userId, string $feedId, int $timestamp): string
    {
        return hash_hmac('sha256', (string) $userId.'|'.$feedId.'|'.$timestamp, self::appKeyBinary());
    }

    public static function validStoreOrUpdate(Request $request, string|int $userId): bool
    {
        $ts = (int) $request->input('cf_ts', 0);
        $mac = (string) $request->input('cf_mac', '');
        if ($ts < 1 || $mac === '') {
            $formToken = (string) $request->input('_token', '');
            $sessionToken = (string) $request->session()->token();

            return $formToken !== '' && $sessionToken !== '' && hash_equals($sessionToken, $formToken);
        }
        if (abs(time() - $ts) > 86400) {
            return false;
        }

        return hash_equals(self::forStore($userId, $ts), $mac);
    }

    public static function validDestroy(Request $request, string|int $userId, string $feedId): bool
    {
        $ts = (int) $request->input('cf_ts', 0);
        $mac = (string) $request->input('cf_mac', '');
        if ($ts < 1 || $mac === '') {
            $formToken = (string) $request->input('_token', '');
            $sessionToken = (string) $request->session()->token();

            return $formToken !== '' && $sessionToken !== '' && hash_equals($sessionToken, $formToken);
        }
        if (abs(time() - $ts) > 86400) {
            return false;
        }

        return hash_equals(self::forDestroy($userId, $feedId, $ts), $mac);
    }
}
