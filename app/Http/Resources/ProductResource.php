<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\File;

class ProductResource extends JsonResource {

    public function toArray ( Request $req ) {

        $files = File::where('table', 'product')->where('column', $this->id);
        $images = FileResource::collection( $files->get() );
        $image = $files->where('type', 'image')->latest()->first()?->url;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'company' => $this->company,
            'phone' => $this->phone,
            'type' => $this->type,
            'language' => $this->language,
            'country' => $this->country,
            'city' => $this->city,
            'street' => $this->street,
            'location' => $this->location,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'old_price' => $this->old_price,
            'new_price' => $this->new_price,
            'description' => $this->description,
            'details' => $this->details,
            'policy' => $this->policy,
            'includes' => $this->includes,
            'notes' => $this->notes,
            'views' => $this->views,
            'rate' => $this->rate,
            'allow_coupons' => $this->allow_coupons,
            'allow_orders' => $this->allow_orders,
            'allow_reviews' => $this->allow_reviews,
            'allow' => $this->allow,
            'active' => $this->active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'info' => ['name' => $this->name, 'image' => $image],
            'image' => $image,
            'images' => $images,
            'category_id' => $this->category_id,
            'vendor_id' => $this->vendor_id,
            'reviews' => count($this->reviews),
            'orders' => count($this->orders),
            'category' => CategoryResource::make( $this->category ),
            'vendor' => UserResource::make( $this->vendor ),
        ];

    }

}
