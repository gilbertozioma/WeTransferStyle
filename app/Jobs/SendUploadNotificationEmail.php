<?php

namespace App\Jobs;

use App\Models\UploadSession;
use Illuminate\Bus\Queueable;
use App\Mail\UploadNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendUploadNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The upload session instance.
     */
    protected UploadSession $session;

    /**
     * Create a new job instance.
     */
    public function __construct(UploadSession $session)
    {
        $this->session = $session;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->session->email_to_notify) {
            Mail::to($this->session->email_to_notify)
                ->send(new UploadNotification($this->session));
        }
    }
}
