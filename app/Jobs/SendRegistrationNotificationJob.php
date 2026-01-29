<?php

namespace App\Jobs;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendRegistrationNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;
    public int $timeout = 120;

    public function __construct(
        public Registration $registration,
        public string $ownerEmail,
        public string $activityTitle
    ) {}

    public function handle(): void
    {
        $registration = $this->registration->loadMissing(['user', 'feed.feedable', 'feed.user']);
        $owner = $registration->feed->user;
        $participant = $registration->user;
        $activity = $registration->feed->feedable;

        try {
            Mail::send('emails.new-registration', [
                'owner' => $owner,
                'participant' => $participant,
                'activity' => $activity,
                'registration' => $registration,
            ], function ($message) {
                $message->to($this->ownerEmail)
                    ->subject('Nouvelle inscription - ' . $this->activityTitle);
            });
        } catch (\Throwable $e) {
            Log::error('Erreur envoi email notification inscription: ' . $e->getMessage(), [
                'registration_id' => $this->registration->id,
            ]);
            throw $e;
        }
    }
}
