<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource {

    public function toArray ( Request $req ) {

        return [
            'id' => $this->id,
            'content' => $this->content,
            'likes' => $this->likes,
            'dislikes' => $this->dislikes,
            'allow_replies' => $this->allow_replies,
            'allow' => $this->allow,
            'active' => $this->active,
            'replies' => count($this->replies),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'vendor_id' => $this->vendor_id,
            'client_id' => $this->client_id,
            'blog_id' => $this->blog_id,
            'blog' => BlogResource::make( $this->blog ),
            'client' => UserResource::make( $this->client ),
            'vendor' => UserResource::make( $this->vendor ),
        ];

    }

}
