<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\File;

class MessageResource extends JsonResource {

    public function toArray ( Request $req ) {

        return [
            'id' => $this->id,
            'content' => $this->content,
            'type' => $this->type,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'product_id' => $this->product_id,
            'star_sender' => $this->star_sender,
            'star_receiver' => $this->star_receiver,
            'readen' => $this->readen,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'readen_at' => $this->readen_at?->format('Y-m-d H:i:s'),
            'file' => FileResource::make( File::where('table', 'message')->where('column', $this->id)->first() ),
        ];

    }

}
