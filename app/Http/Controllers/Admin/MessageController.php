<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\RelationResource;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserResource;
use App\Models\Relation;
use App\Models\Message;
use App\Models\User;
use App\Events\ChatBox;

class MessageController extends Controller {

    public $admin_id = 1;

    public function _active_ ( $user ) {

        Message::where('receiver_id', $this->admin_id)
            ->where('sender_id', $user->id)
            ->where('removed_receiver', false)
            ->where('readen', false)
            ->update(['readen' => true, 'readen_at' => $this->date()]);
        
        event(new ChatBox($this->user()->id, $user->id, 'active', UserResource::make($user), '', true));

    }
    public function _new_ ( $user_id ) {

        $relation = Relation::where('sender_id', $this->admin_id)
                    ->where('receiver_id', $user_id)
                    ->orWhere('receiver_id', $this->admin_id)
                    ->where('sender_id', $user_id)
                    ->first();

        if ( $relation) $relation->update(['removed_sender' => false, 'removed_receiver' => false]);
        else $relation = Relation::create(['sender_id' => $this->admin_id, 'receiver_id' => $user_id]);

        return $relation;

    }
    public function relations ( Request $req ) {

        $relations = Relation::where('sender_id', $this->admin_id)
                        ->where('removed_sender', false)
                        ->orWhere('receiver_id', $this->admin_id)
                        ->where('removed_receiver', false)
                        ->get();

        $relations = RelationResource::collection($relations);
        $users = User::where('role', '!=', 1)->where('active', true)->where('allow_messages', true)->get();
        $users = UserResource::collection( $users );

        return $this->success(['relations' => $relations, 'users' => $users]);

    }
    public function messages ( Request $req, User $user ) {

        self::_active_($user);

        $messages = Message::where('sender_id', $this->admin_id)
                        ->where('receiver_id', $user->id)
                        ->where('removed_sender', false)
                        ->orWhere('receiver_id', $this->admin_id)
                        ->where('sender_id', $user->id)
                        ->where('removed_receiver', false)
                        ->orderBy('id', 'desc')->take(50)->get()->reverse();

        $messages = MessageResource::collection($messages);
        return $this->success(['messages' => $messages]);

    }
    public function send ( Request $req, User $user ) {

        if ( !$user->active || !$user->allow_messages ) return $this->failed(['user' => 'access denied']);
        $relation = self::_new_($user->id);

        $data = ['sender_id' => $this->admin_id, 'receiver_id' => $user->id, 'content' => $req->content, 'type' => $req->type];
        $message = Message::create($data);
        $this->upload_files( [$req->file('file')], 'message', $message->id );

        $message = MessageResource::make( Message::find($message->id) );
        event(new ChatBox($this->user()->id, $user->id, 'message', UserResource::make($user), $message, true));
        return $this->success(['message' => $message]);

    }
    public function active ( Request $req, User $user ) {

        self::_active_($user);
        return $this->success();

    }
    public function delete ( Request $req, User $user ) {

        Relation::where('sender_id', $this->admin_id)->where('receiver_id', $user->id)->where('removed_sender', false)->update(['removed_sender' => true]);
        Relation::where('receiver_id', $this->admin_id)->where('sender_id', $user->id)->where('removed_receiver', false)->update(['removed_receiver' => true]);
        Message::where('sender_id', $this->admin_id)->where('receiver_id', $user->id)->where('removed_sender', false)->update(['removed_sender' => true]);
        Message::where('receiver_id', $this->admin_id)->where('sender_id', $user->id)->where('removed_receiver', false)->update(['removed_receiver' => true]);
        
        event(new ChatBox($this->user()->id, $user->id, 'delete', UserResource::make($user), '', true));
        return $this->success();

    }
    public function delete_message ( Request $req, Message $message ) {

        if ( $message->sender_id == $this->admin_id ) { $message->removed_sender = true; $user = $message->receiver; }
        if ( $message->receiver_id == $this->admin_id ) { $message->removed_receiver = true; $user = $message->sender; }
        $message->save();

        event(new ChatBox($this->user()->id, $user->id, 'delete_message', '', $message, true));
        return $this->success();

    }
    public function archive ( Request $req, User $user ) {

        Relation::where('sender_id', $this->admin_id)->where('receiver_id', $user->id)->where('archived_sender', false)->update(['archived_sender' => true]);
        Relation::where('receiver_id', $this->admin_id)->where('sender_id', $user->id)->where('archived_receiver', false)->update(['archived_receiver' => true]);
        
        event(new ChatBox($this->user()->id, $this->admin_id, 'archive', UserResource::make($user)));
        return $this->success();

    }
    public function unarchive ( Request $req, User $user ) {

        Relation::where('sender_id', $this->admin_id)->where('receiver_id', $user->id)->where('archived_sender', true)->update(['archived_sender' => false]);
        Relation::where('receiver_id', $this->admin_id)->where('sender_id', $user->id)->where('archived_receiver', true)->update(['archived_receiver' => false]);
        
        event(new ChatBox($this->user()->id, $this->admin_id, 'unarchive', UserResource::make($user)));
        return $this->success();

    }
    public function star_message ( Request $req, Message $message ) {

        if ( $message->sender_id == $this->admin_id ) $message->star_sender = true;
        if ( $message->receiver_id == $this->admin_id ) $message->star_receiver = true;
        $message->save();

        event(new ChatBox($this->user()->id, $this->admin_id, 'star', '', $message));
        return $this->success();

    }
    public function unstar_message ( Request $req, Message $message ) {

        if ( $message->sender_id == $this->admin_id ) $message->star_sender = false;
        if ( $message->receiver_id == $this->admin_id ) $message->star_receiver = false;
        $message->save();

        event(new ChatBox($this->user()->id, $this->admin_id, 'unstar', '', $message));
        return $this->success();

    }

}
