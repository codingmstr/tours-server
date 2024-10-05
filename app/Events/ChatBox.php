<?php

namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatBox implements ShouldBroadcast {

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sender;
    public $receiver;
    public $action;
    public $user;
    public $message;
    public $to_all;

    public function __construct ( $sender=0, $receiver=0, $action='', $user='', $message='', $to_all=false ) {

        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->action = $action;
        $this->user = $user;
        $this->message = $message;
        $this->to_all = $to_all;

    }
    public function broadcastOn () {

        $channels = [new PrivateChannel("chat.{$this->receiver}")];
        if ( $this->to_all ) array_push($channels, new PrivateChannel('chat.1'));
        return $channels;

    }
    public function broadcastWith () {

        return [
            'sender' => $this->sender,
            'receiver' => $this->receiver,
            'action' => $this->action,
            'user' => $this->user,
            'message' => $this->message,
        ];

    }
    public function broadcastAs () {

        return 'chat.box';

    }
    public function broadcastWhen () {

        return true;

    }

}
