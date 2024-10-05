<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\File;

class ClientResource extends JsonResource {

    public function toArray ( Request $req ) {

        $files = File::where('table', 'user')->where('column', $this->id);
        $image = $files->where('type', 'image')->latest()->first()?->url;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'age' => $this->age,
            'company' => $this->company,
            'phone' => $this->phone,
            'language' => $this->language,
            'country' => $this->country,
            'city' => $this->city,
            'street' => $this->street,
            'location' => $this->location,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'currency' => $this->currency,
            'ip' => $this->ip,
            'agent' => $this->agent,
            'balance' => $this->balance,
            'notes' => $this->notes,
            'allow_messages' => $this->allow_messages,
            'allow_contacts' => $this->allow_contacts,
            'allow_coupons' => $this->allow_coupons,
            'allow_orders' => $this->allow_orders,
            'allow_reviews' => $this->allow_reviews,
            'allow_likes' => $this->allow_likes,
            'allow_dislikes' => $this->allow_dislikes,
            'allow_comments' => $this->allow_comments,
            'allow_replies' => $this->allow_replies,
            'allow_reports' => $this->allow_reports,
            'allow_statistics' => $this->allow_statistics,
            'allow_login' => $this->allow_login,
            'active' => $this->active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'login_at' => $this->login_at?->format('Y-m-d H:i:s'),
            'image' => $image,
            'info' => ['name' => $this->name, 'image' => $image],
            'orders' => count($this->orders),
            'reviews' => count($this->reviews),
        ];

    }

}
