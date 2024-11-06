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

class MessageController extends Controller {

    public function _active_ ( $user, $product ) {

        Message::where('receiver_id', $this->user()->id)
            ->where('sender_id', $user->id)
            ->where('removed_receiver', false)
            ->where('product_id', $product->id)
            ->where('readen', false)
            ->update(['readen' => true, 'readen_at' => $this->date()]);
        
        event(new ChatBox($this->user()->id, $user->id, 'active', UserResource::make($user), ''));

    }
    public function _new_ ( $user ) {

        $relation = Relation::where('sender_id', $this->user()->id)
                    ->where('receiver_id', $user->id)
                    ->orWhere('receiver_id', $this->user()->id)
                    ->where('sender_id', $user->id)
                    ->first();

        if ( $relation) $relation->update(['removed_sender' => false, 'removed_receiver' => false]);
        else $relation = Relation::create(['sender_id' => $this->user()->id, 'receiver_id' => $user->id]);
        return $relation;

    }
    public function index ( Request $req, User $user, Product $product ) {

        if ( !$product->active || !$user->active ) return $this->failed();
      
        self::_active_($user, $product);

        $messages = Message::where('sender_id', $this->user()->id)
                        ->where('receiver_id', $user->id)
                        ->where('removed_sender', false)
                        ->where('product_id', $product->id)
                        ->orWhere('receiver_id', $this->user()->id)
                        ->where('sender_id', $user->id)
                        ->where('removed_receiver', false)
                        ->where('product_id', $product->id)
                        ->orderBy('id', 'desc')->take(50)->get()->reverse();

        $product = ProductResource::make( $product );
        $vendor = UserResource::make( $user );
        $messages = MessageResource::collection( $messages );
        $data = ['product' => $product, 'vendor' => $vendor, 'messages' => $messages];
        return $this->success($data);

    }
    public function send ( Request $req, User $user, Product $product ) {

        if ( !$user->active || !$user->allow_messages ) return $this->failed(['user' => 'access denied']);
        $relation = self::_new_($user);

        $data = [
            'product_id' => $product->id,
            'sender_id' => $this->user()->id,
            'receiver_id' => $user->id,
            'content' => $req->content,
            'type' => $req->type
        ];
        $message = Message::create($data);
        $this->upload_files( [$req->file('file')], 'message', $message->id );

        $message = MessageResource::make( Message::find($message->id) );
        event(new ChatBox($this->user()->id, $user->id, 'message', UserResource::make($user), $message));
        return $this->success(['message' => $message]);

    }
    public function active ( Request $req, User $user, Product $product ) {

        self::_active_($user, $product);
        return $this->success();

    }

}
