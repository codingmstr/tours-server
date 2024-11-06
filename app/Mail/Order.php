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
use App\Models\Setting;

class Order extends Mailable implements ShouldQueue {

    use Queueable, SerializesModels;

    public $user;
    public $order;
    public $frontUrl;

    public function __construct ( $user, $order ) {

        $this->user = $user;
        $this->order = $order;
        $this->frontUrl = 'https://kimitours.com';

    }
    public function content () {

        $data = [
            'user' => $this->user,
            'order' => $this->order,
            'settings' => Setting::find(1),
            'frontUrl' => $this->frontUrl,
        ];
        return new Content(view: 'mails.order', with: $data);

    }

}
