<?php

namespace App\Http\Middleware;

use App\Support\CampaignWriteMac;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyCsrfToken extends Middleware
{
    /**
     * URIs exclues (webhooks CinetPay : POST sans token CSRF).
     *
     * @var array<int, string>
     */
    protected $except = [
        'payments/notify',
        'subscriptions/notify',
        // Création campagne : validée par signature HMAC (cf_ts / cf_mac) — évite 419 si session/token CSRF incohérents
        'creator/campaigns/store',
    ];

    /**
     * Doit exclure cette requête du contrôle CSRF.
     * Chemins + noms de route : plus robuste si l’URL change ou sous-dossier.
     */
    protected function inExceptArray($request): bool
    {
        return $this->shouldPassWithoutCsrf($request)
            || parent::inExceptArray($request);
    }

    protected function shouldPassWithoutCsrf(Request $request): bool
    {
        if ($request->routeIs('payments.notify', 'subscriptions.notify')) {
            return true;
        }

        $path = trim($request->path(), '/');

        if (str_ends_with($path, 'payments/notify')
            || str_ends_with($path, 'subscriptions/notify')
            || $request->routeIs('campaigns.store')) {
            return true;
        }

        $user = Auth::guard('web')->user();
        if ($user) {
            $uid = $user->getAuthIdentifier();
            if ($request->routeIs('campaigns.update') && CampaignWriteMac::validStoreOrUpdate($request, $uid)) {
                return true;
            }
            if ($request->routeIs('campaigns.destroy')) {
                $fid = $request->route('uuid');
                if (is_string($fid) && $fid !== '' && CampaignWriteMac::validDestroy($request, $uid, $fid)) {
                    return true;
                }
            }
        }

        return false;
    }
}
