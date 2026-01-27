<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Services\ReceiptService;
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

        $sent = app(ReceiptService::class)->sendReceiptEmail($registration);
        if (!$sent) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Impossible d\'envoyer l\'email.'], 500);
            }
            return back()->with('error', 'Impossible d\'envoyer le reçu par email.');
        }

        $user = $registration->user;
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Reçu envoyé à ' . $user->email]);
        }
        return back()->with('success', 'Reçu envoyé à ' . $user->email);
    }
}
