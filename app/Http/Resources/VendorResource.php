<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource {

    public function toArray ( Request $req ) {

        return array_merge([
            'products' => count($this->vendor_products),
            'reviews' => count($this->vendor_reviews),
            'orders' => count($this->vendor_orders),
            'pending_orders' => count($this->vendor_orders?->where('status', 'pending')),
            'rate' => $this->vendor_reviews?->avg('rate') ?? 0,
        ], (new UserResource($this))->toArray($req));

    }

}
