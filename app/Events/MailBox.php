<?php

namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MailBox implements ShouldBroadcast {

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $receiver;
    public $mail;

    public function __construct ( $receiver=0, $mail='' ) {

        $this->receiver = $receiver;
        $this->mail = $mail;

    }
    public function broadcastOn () {

        return [
            new PrivateChannel("mail.{$this->receiver}")
        ];

    }
    public function broadcastWith () {

        return [
            'mail' => $this->mail,
        ];

    }
    public function broadcastAs () {

        return 'mail.box';

    }

}
