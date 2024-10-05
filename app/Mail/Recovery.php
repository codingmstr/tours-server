<?php

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Storage;

class Recovery extends Mailable implements ShouldQueue {

    use Queueable, SerializesModels;

    public $user;
    public $token;

    public function __construct ( $user, $token ) {

        $this->user = $user;
        $this->token = $token;

    }
    public function envelope () {

        return new Envelope(
            subject: 'Reset Password',
            tags: ['recovery', 'reset password'],
            metadata: [
                'user' => $this->user->name,
                'token' => $this->token,
            ],
        );

    }
    public function content () {

        $data = ['user' => $this->user, 'token' => $this->token];
        return new Content(view: 'mails.recovery', with: $data);

    }

}
