<?php

namespace App\Mail;

use App\Models\UploadSession;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Illuminate\Queue\SerializesModels;

class UploadNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The upload session instance.
     */
    public UploadSession $session;

    /**
     * Create a new message instance.
     */
    public function __construct(UploadSession $session)
    {
        $this->session = $session;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $downloadUrl = URL::to('/api/download/' . $this->session->token);

        return $this->subject('Files have been shared with you')
            ->markdown('emails.upload-notification', [
                'session' => $this->session,
                'downloadUrl' => $downloadUrl,
                'expiresAt' => $this->session->expires_at->format('F j, Y, g:i a'),
                'hasPassword' => (bool) $this->session->password,
            ]);
    }
}
