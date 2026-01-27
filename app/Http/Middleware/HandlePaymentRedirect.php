<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandlePaymentRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Si la session contient un registration_id, rediriger vers le paiement
        if (session()->has('registration_id')) {
            $registrationId = session('registration_id');
            session()->forget('registration_id');
            
            return redirect()->route('payment.initiate')
                ->with('registration_id', $registrationId);
        }

        return $response;
    }
} 