<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\File;

class UserResource extends JsonResource {

    public function toArray ( Request $req ) {

        $files = File::where('table', 'user')->where('column', $this->id);
        $image = $files->where('type', 'image')->latest()->first()?->url;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'company' => $this->company,
            'phone' => $this->phone,
            'age' => $this->age,
            'language' => $this->language,
            'country' => $this->country,
            'city' => $this->city,
            'street' => $this->street,
            'location' => $this->location,
            'postal' => $this->postal,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'currency' => $this->currency,
            'gender' => $this->gender,
            'description' => $this->description,
            'ip' => $this->ip,
            'agent' => $this->agent,
            'notes' => $this->notes,
            'withdraw_balance' => $this->withdraw_balance,
            'pending_balance' => $this->pending_balance,
            'buy_balance' => $this->buy_balance,
            'balance' => $this->withdraw_balance + $this->pending_balance + $this->buy_balance,
            'withdraws' => $this->withdraws,
            'deposits' => $this->deposits,
            'earned_points' => $this->earned_points,
            'points' => $this->points,
            'days' => $this->days,
            'times' => $this->times,
            'allow_categories' => $this->allow_categories,
            'allow_products' => $this->allow_products,
            'allow_coupons' => $this->allow_coupons,
            'allow_orders' => $this->allow_orders,
            'allow_reviews' => $this->allow_reviews,
            'allow_messages' => $this->allow_messages,
            'allow_mails' => $this->allow_mails,
            'allow_contacts' => $this->allow_contacts,
            'allow_reports' => $this->allow_reports,
            'allow_clients' => $this->allow_clients,
            'allow_vendors' => $this->allow_vendors,
            'allow_clients_wallet' => $this->allow_clients_wallet,
            'allow_vendors_wallet' => $this->allow_vendors_wallet,
            'allow_statistics' => $this->allow_statistics,
            'allow_likes' => $this->allow_likes,
            'allow_dislikes' => $this->allow_dislikes,
            'allow_blogs' => $this->allow_blogs,
            'allow_comments' => $this->allow_comments,
            'allow_replies' => $this->allow_replies,
            'allow_login' => $this->allow_login,
            'activate_email' => $this->activate_email,
            'activate_phone' => $this->activate_phone,
            'activate_identity' => $this->activate_identity,
            'premium' => $this->premium,
            'available' => $this->available,
            'active' => $this->active,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'login_at' => $this->login_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'image' => $image,
            'info' => ['name' => $this->name, 'image' => $image],
        ];

    }

}
