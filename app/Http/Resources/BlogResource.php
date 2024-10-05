<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\File;

class BlogResource extends JsonResource {

    public function toArray ( Request $req ) {

        $files = File::where('table', 'blog')->where('column', $this->id);
        $images = FileResource::collection( $files->get() );
        $image = $files->where('type', 'image')->latest()->first()?->url;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'content' => $this->content,
            'notes' => $this->notes,
            'company' => $this->company,
            'phone' => $this->phone,
            'language' => $this->language,
            'country' => $this->country,
            'city' => $this->city,
            'street' => $this->street,
            'location' => $this->location,
            'views' => $this->views,
            'likes' => $this->likes,
            'dislikes' => $this->dislikes,
            'allow_comments' => $this->allow_comments,
            'allow_replies' => $this->allow_replies,
            'allow_likes' => $this->allow_likes,
            'allow_dislikes' => $this->allow_dislikes,
            'allow' => $this->allow,
            'active' => $this->active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'comments' => count($this->comments),
            'replies' => count($this->replies),
            'info' => ['title' => $this->title, 'image' => $image],
            'image' => $image,
            'images' => $images,
            'vendor' => UserResource::make( $this->vendor ),
        ];

    }

}
