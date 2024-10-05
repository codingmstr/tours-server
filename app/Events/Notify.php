<?php

namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Notify implements ShouldBroadcast {

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sender;
    public $message;

    public function __construct ( $sender=0, $message='') {

        $this->sender = $sender;
        $this->message = $message;

    }
    public function broadcastOn () {

        return [
            new PrivateChannel("notification")
        ];

    }
    public function broadcastWith () {

        return [
            'sender' => $this->sender,
            'message' => $this->message,
        ];

    }
    public function broadcastAs () {

        return 'notify.box';

    }

}
