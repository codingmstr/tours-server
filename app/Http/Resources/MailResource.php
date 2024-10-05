<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\File;

class MailResource extends JsonResource {

    public function toArray ( Request $req ) {

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'star_sender' => $this->star_sender,
            'star_receiver' => $this->star_receiver,
            'important_sender' => $this->important_sender,
            'important_receiver' => $this->important_receiver,
            'archived_sender' => $this->archived_sender,
            'archived_receiver' => $this->archived_receiver,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'readen_at' => $this->readen_at?->format('Y-m-d H:i:s'),
            'readen' => $this->readen,
            'sender' => UserResource::make($this->sender),
            'receiver' => UserResource::make($this->receiver),
            'files' => FileResource::collection( File::where('table', 'mail')->where('column', $this->id)->get() ),
        ];

    }

}
