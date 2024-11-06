<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\File;

class SettingResource extends JsonResource {

    public function toArray ( Request $req ) {

        $logo = File::where('table', 'logo')->where('column', $this->id)->latest()->first()?->url;
        $hero = FileResource::collection( File::where('table', 'slider')->where('column', $this->id)->get() );

        return [
            'logo' => $logo,
            'hero' => $hero,
            'name' => $this->name,
            'email' => $this->email,
            'email1' => $this->email1,
            'phone' => $this->phone,
            'phone1' => $this->phone1,
            'country' => $this->country,
            'city' => $this->city,
            'street' => $this->street,
            'location' => $this->location,
            'language' => $this->language,
            'currency' => $this->currency,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'postal' => $this->postal,
            'company' => $this->company,
            'code' => $this->code,
            'theme' => $this->theme,
            'facebook' => $this->facebook,
            'whatsapp' => $this->whatsapp,
            'youtube' => $this->youtube,
            'linkedin' => $this->linkedin,
            'instagram' => $this->instagram,
            'telegram' => $this->telegram,
            'twitter' => $this->twitter,
            'balance' => $this->balance,
            'profit' => $this->profit,
            'income' => $this->income,
            'expenses' => $this->expenses,
            'deposits' => $this->deposits,
            'withdraws' => $this->withdraws,
            'allow_mails' => $this->allow_mails,
            'allow_messages' => $this->allow_messages,
            'allow_notifications' => $this->allow_notifications,
            'allow_categories' => $this->allow_categories,
            'allow_products' => $this->allow_products,
            'allow_coupons' => $this->allow_coupons,
            'allow_orders' => $this->allow_orders,
            'allow_blogs' => $this->allow_blogs,
            'allow_comments' => $this->allow_comments,
            'allow_replies' => $this->allow_replies,
            'allow_reviews' => $this->allow_reviews,
            'allow_contacts' => $this->allow_contacts,
            'allow_reports' => $this->allow_reports,
            'allow_emails' => $this->allow_emails,
            'allow_vendors' => $this->allow_vendors,
            'allow_clients' => $this->allow_clients,
            'allow_logins' => $this->allow_logins,
            'allow_deposits' => $this->allow_deposits,
            'allow_withdraws' => $this->allow_withdraws,
            'allow_payments' => $this->allow_payments,
            'allow_pay_later' => $this->allow_pay_later,
            'running' => $this->running,
            'about' => $this->about,
            'terms' => $this->terms,
            'policy' => $this->policy,
            'services' => $this->services,
            'help' => $this->help,
        ];

    }
}
