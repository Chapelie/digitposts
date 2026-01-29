<?php

namespace App\Http\Controllers;

use App\Jobs\SendReceiptEmailJob;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    /**
     * Afficher le reçu PDF dans le navigateur (inline)
     */
    public function show(string $registrationId)
    {
        $registration = Registration::with(['user', 'feed.feedable'])
            ->where('id', $registrationId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($registration->payment_status !== 'paid') {
            abort(404, 'Aucun reçu pour cette inscription.');
        }

        $activity = $registration->feed->feedable;
        $pdf = \PDF::loadView('dashboard.receipts.receipt-pdf', [
            'registration' => $registration,
            'activity' => $activity,
            'generatedAt' => now(),
        ]);

        $filename = 'recu_' . ($registration->payment_transaction_id ?? $registration->id) . '.pdf';
        return $pdf->stream($filename);
    }

    /**
     * Télécharger le reçu PDF
     */
    public function download(string $registrationId)
    {
        $registration = Registration::with(['user', 'feed.feedable'])
            ->where('id', $registrationId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($registration->payment_status !== 'paid') {
            abort(404, 'Aucun reçu pour cette inscription.');
        }

        $activity = $registration->feed->feedable;
        $pdf = \PDF::loadView('dashboard.receipts.receipt-pdf', [
            'registration' => $registration,
            'activity' => $activity,
            'generatedAt' => now(),
        ]);

        $filename = 'recu_' . ($registration->payment_transaction_id ?? $registration->id) . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Envoyer le reçu par email
     */
    public function sendEmail(Request $request, string $registrationId)
    {
        $registration = Registration::with(['user', 'feed.feedable'])
            ->where('id', $registrationId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($registration->payment_status !== 'paid') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Aucun reçu pour cette inscription.'], 404);
            }
            return back()->with('error', 'Aucun reçu pour cette inscription.');
        }

        SendReceiptEmailJob::dispatch($registration);
        $user = $registration->user;
        $msg = 'Le reçu sera envoyé à ' . $user->email;
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }
        return back()->with('success', $msg);
    }
}
