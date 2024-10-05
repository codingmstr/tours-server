<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Message;

class RelationResource extends JsonResource {

    public function unreaden ( $current_id, $user_id ) {

        return Message::where('receiver_id', $current_id)
                ->where('sender_id', $user_id)
                ->where('readen', false)
                ->count();

    }
    public function last_messages ( $current_id, $user_id ) {

        $messages = Message::where('sender_id', $current_id)
            ->where('receiver_id', $user_id)
            ->where('removed_sender', false)
            ->orWhere('receiver_id', $current_id)
            ->where('sender_id', $user_id)
            ->where('removed_receiver', false)
            ->orderBy('id', 'desc')->take(1)->get()->reverse();

        return MessageResource::collection($messages);

    }
    public function toArray ( Request $req ) {

        $current_id = 1;
        $user = $this->sender_id == $current_id ? $this->receiver : $this->sender;

        return [
            'id' => $this->id,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'archived' => $this->sender_id == $current_id ? $this->archived_sender : $this->archived_receiver,
            'unreaden' => $this->unreaden($current_id, $user->id),
            'user' => UserResource::make($user),
            'messages' => $this->last_messages($current_id, $user->id),
        ];

    }

}
