<?php

namespace App\Services;

use App\Models\Registration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReceiptService
{
    /**
     * Envoyer le reçu par email au participant (après paiement ou à la demande)
     */
    public function sendReceiptEmail(Registration $registration): bool
    {
        $registration->loadMissing(['user', 'feed.feedable']);
        $user = $registration->user;
        $activity = $registration->feed->feedable;

        $pdf = \PDF::loadView('dashboard.receipts.receipt-pdf', [
            'registration' => $registration,
            'activity' => $activity,
            'generatedAt' => now(),
        ]);
        $pdfContent = $pdf->output();
        $filename = 'recu_' . ($registration->payment_transaction_id ?? $registration->id) . '.pdf';

        try {
            Mail::send('emails.receipt', [
                'registration' => $registration,
                'activity' => $activity,
                'user' => $user,
            ], function ($message) use ($user, $activity, $pdfContent, $filename) {
                $message->to($user->email)
                    ->subject('Votre reçu - ' . $activity->title);
                $message->attachData($pdfContent, $filename, ['mime' => 'application/pdf']);
            });
            return true;
        } catch (\Exception $e) {
            Log::error('Erreur envoi reçu par email: ' . $e->getMessage(), [
                'registration_id' => $registration->id,
            ]);
            return false;
        }
    }
}
