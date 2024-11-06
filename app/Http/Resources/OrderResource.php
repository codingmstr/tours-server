<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource {

    public function toArray ( Request $req ) {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'address' => $this->address,
            'company' => $this->company,
            'phone' => $this->phone,
            'language' => $this->language,
            'country' => $this->country,
            'city' => $this->city,
            'street' => $this->street,
            'location' => $this->location,
            'currency' => $this->currency,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'postal' => $this->postal,
            'notes' => $this->notes,
            'price' => $this->price,
            'coupon_discount' => $this->coupon_discount,
            'coupon_code' => $this->coupon_code,
            'secret_key' => $this->secret_key,
            'paid' => $this->paid,
            'status' => $this->status,
            'deleted' => $this->deleted,
            'active' => $this->active,
            'paid_at' => $this->paid_at?->format('Y-m-d H:i:s'),
            'confirmed_at' => $this->confirmed_at?->format('Y-m-d H:i:s'),
            'cancelled_at' => $this->cancelled_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'ordered_at' => $this->ordered_at?->format('Y-m-d'),
            'vendor_id' => $this->vendor_id,
            'client_id' => $this->client_id,
            'coupon_id' => $this->coupon_id,
            'product_id' => $this->product_id,
            'product' => ProductResource::make( $this->product ),
            'coupon' => CouponResource::make( $this->coupon ),
            'client' => UserResource::make( $this->client ),
            'vendor' => UserResource::make( $this->vendor ),
        ];

    }

}
