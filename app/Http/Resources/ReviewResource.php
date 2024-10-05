<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource {

    public function toArray ( Request $req ) {

       return [
            'id' => $this->id,
            'content' => $this->content,
            'rate' => $this->rate,
            'allow' => $this->allow,
            'active' => $this->active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'vendor_id' => $this->vendor_id,
            'client_id' => $this->client_id,
            'product_id' => $this->product_id,
            'order_id' => $this->order_id,
            'product' => ProductResource::make( $this->product ),
            'client' => UserResource::make( $this->client ),
            'vendor' => UserResource::make( $this->vendor ),
        ];

    }

}
