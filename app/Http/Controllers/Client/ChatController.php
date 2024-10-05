<?php

namespace App\Http\Controllers\client;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\Relation;
use App\Models\Message;
use App\Models\Product;
use App\Models\User;
use App\Events\ChatBox;

class ChatController extends Controller {

    public $admin_id = 1;

    public function _active_ ( $user ) {

        Message::where('receiver_id', $user->id)
            ->where('sender_id', $this->admin_id)
            ->where('removed_receiver', false)
            ->where('readen', false)
            ->update(['readen' => true, 'readen_at' => $this->date()]);
        
        event(new ChatBox($user->id, $this->admin_id, 'active', UserResource::make($user), ''));

    }
    public function _new_ ( $user ) {

        $relation = Relation::where('sender_id', $user->id)
                    ->where('receiver_id', $this->admin_id)
                    ->orWhere('receiver_id', $user->id)
                    ->where('sender_id', $this->admin_id)
                    ->first();

        if ( $relation) $relation->update(['removed_sender' => false, 'removed_receiver' => false]);
        else $relation = Relation::create(['sender_id' => $user->id, 'receiver_id' => $this->admin_id]);
        return $relation;

    }
    public function index ( Request $req ) {

        $messages = Message::where('sender_id', $this->user()->id)
                        ->where('receiver_id', $this->admin_id)
                        ->where('removed_sender', false)
                        ->orWhere('receiver_id', $this->user()->id)
                        ->where('sender_id', $this->admin_id)
                        ->where('removed_receiver', false)
                        ->orderBy('id', 'desc')->take(50)->get()->reverse();

        $messages = MessageResource::collection( $messages );
        return $this->success(['messages' => $messages]);

    }
    public function active ( Request $req ) {

        self::_active_($this->user());
        return $this->success();

    }
    public function send ( Request $req ) {

        $relation = self::_new_($this->user());

        $data = ['sender_id' => $this->user()->id, 'receiver_id' => $this->admin_id, 'content' => $req->content, 'type' => $req->type];
        $message = Message::create($data);
        $this->upload_files( [$req->file('file')], 'message', $message->id );

        $message = MessageResource::make( Message::find($message->id) );
        event(new ChatBox($this->user()->id, $this->admin_id, 'message', UserResource::make($this->user()), $message));
        return $this->success(['message' => $message]);

    }

}
