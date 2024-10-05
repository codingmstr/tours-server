<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource {

    public function toArray ( Request $req ) {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'discount' => $this->discount,
            'notes' => $this->notes,
            'allow' => $this->allow,
            'active' => $this->active,
            'allow_orders' => $this->allow_orders,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'orders' => count($this->orders),
            'vendor_id' => $this->vendor_id,
            'client_id' => $this->client_id,
            'category_id' => $this->category_id,
            'product_id' => $this->product_id,
            'vendor' => UserResource::make( $this->vendor ),
            'client' => UserResource::make( $this->client ),
            'category' => CategoryResource::make( $this->category ),
            'product' => ProductResource::make( $this->product ),
        ];

    }

}
