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
            'meeting' => $this->meeting,
            'rules' => $this->rules,
            'availability' => $this->availability,
            'more_info' => $this->more_info,
            'includes' => $this->includes,
            'expected' => $this->expected,
            'days' => $this->days,
            'times' => $this->times,
            'notes' => $this->notes,
            'duration' => $this->duration,
            'max_persons' => $this->max_persons,
            'max_orders' => $this->max_orders,
            'views' => $this->views,
            'pay_later' => $this->pay_later,
            'allow_reviews' => $this->allow_reviews,
            'allow_orders' => $this->allow_orders,
            'allow_coupons' => $this->allow_coupons,
            'allow_cancel' => $this->allow_cancel,
            'allow' => $this->allow,
            'active' => $this->active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'info' => ['name' => $this->name, 'image' => $image],
            'image' => $image,
            'images' => $images,
            'category_id' => $this->category_id,
            'vendor_id' => $this->vendor_id,
            'category' => CategoryResource::make( $this->category ),
            'vendor' => UserResource::make( $this->vendor ),
            'rate' => $this->reviews?->avg('rate') ?? 0,
            'reviews' => count($this->reviews),
            'orders' => count($this->orders),
            'pending_orders' => count($this->orders?->where('status', 'pending')),
        ];

    }

}
