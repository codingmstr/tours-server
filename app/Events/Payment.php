<?php

namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Payment implements ShouldBroadcast {

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $transaction;

    public function __construct ( $user_id, $transaction ) {

        $this->user_id = $user_id;
        $this->transaction = $transaction;

    }
    public function broadcastOn () {

        return [
            new PrivateChannel("payment.{$this->user_id}")
        ];

    }
    public function broadcastWith () {

        return [
            'transaction' => $this->transaction,
        ];

    }
    public function broadcastAs () {

        return 'payment.completed';

    }

}
