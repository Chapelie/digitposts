<?php

namespace App\Jobs;

use App\Models\Registration;
use App\Services\ReceiptService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendReceiptEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;
    public int $timeout = 120;

    public function __construct(
        public Registration $registration
    ) {}

    public function handle(ReceiptService $receiptService): void
    {
        $this->registration->loadMissing(['user', 'feed.feedable']);
        $receiptService->sendReceiptEmail($this->registration);
    }
}
