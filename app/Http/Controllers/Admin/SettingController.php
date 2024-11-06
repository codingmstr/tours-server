<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Review;
use App\Models\Contact;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Reply;
use App\Models\Report;
use App\Models\Mail;
use App\Models\Relation;
use App\Models\Message;
use App\Models\User;
use App\Models\File;

class SettingController extends Controller {

    public function index ( Request $req ) {

        $settings = SettingResource::make( Setting::find(1) );
        return $this->success(['settings' => $settings]);

    }
    public function update ( Request $req ) {

        $setting = Setting::find(1);
        if ( !$setting ) return $this->failed();

        $location = $this->get_location("{$req->street}, {$req->city}, {$req->country}");

        $data = [
            'name' => $this->string($req->name),
            'email' => $this->string($req->email),
            'email1' => $this->string($req->email1),
            'phone' => $this->string($req->phone),
            'phone1' => $this->string($req->phone1),
            'company' => $this->string($req->company),
            'code' => $this->string($req->code),
            'language' => $this->string($req->language),
            'theme' => $this->string($req->theme),
            'country' => $this->string($req->country),
            'city' => $this->string($req->city),
            'street' => $this->string($req->street),
            'location' => $this->string($req->location),
            'currency' => $this->string($req->currency),
            'postal' => $this->string($req->postal),
            'longitude' => $this->string($req->longitude) ?? $location['longitude'],
            'latitude' => $this->string($req->latitude) ?? $location['latitude'],
            'facebook' => $this->string($req->facebook),
            'whatsapp' => $this->string($req->whatsapp),
            'youtube' => $this->string($req->youtube),
            'linkedin' => $this->string($req->linkedin),
            'instagram' => $this->string($req->instagram),
            'telegram' => $this->string($req->telegram),
            'twitter' => $this->string($req->twitter),
        ];
        
        $setting->update($data);
        $this->report($req, 'setting', 0, 'update', 'admin');
        return $this->success();

    }
    public function content ( Request $req ) {

        $setting = Setting::find(1);

        $data = [
            'about' => $this->string($req->about),
            'terms' => $this->string($req->terms),
            'policy' => $this->string($req->policy),
            'services' => $this->string($req->services),
            'help' => $this->string($req->help),
        ];

        $setting->update($data);

        $slider = array_filter($req->allFiles(), function ( $key, $value ) { return $value !== 'logo_file'; }, ARRAY_FILTER_USE_BOTH);
        $this->upload_files( $slider, 'slider', $setting->id );
        $this->delete_files( $this->parse($req->deleted_files), 'slider' );

        if ( $req->file('logo_file') ) {

            $file_id = File::where('table', 'logo')->where('column', $setting->id)->first()?->id;
            $this->delete_files([$file_id], 'logo');
            $this->upload_files([$req->file('logo_file')], 'logo', $setting->id);

        }

        $this->report($req, 'setting', 0, 'update', 'admin');
        return $this->success();

    }
    public function option ( Request $req ) {

        $setting = Setting::find(1);

        $data = [
            'allow_mails' => $this->bool($req->allow_mails),
            'allow_messages' => $this->bool($req->allow_messages),
            'allow_notifications' => $this->bool($req->allow_notifications),
            'allow_categories' => $this->bool($req->allow_categories),
            'allow_products' => $this->bool($req->allow_products),
            'allow_coupons' => $this->bool($req->allow_coupons),
            'allow_orders' => $this->bool($req->allow_orders),
            'allow_blogs' => $this->bool($req->allow_blogs),
            'allow_comments' => $this->bool($req->allow_comments),
            'allow_replies' => $this->bool($req->allow_replies),
            'allow_reviews' => $this->bool($req->allow_reviews),
            'allow_contacts' => $this->bool($req->allow_contacts),
            'allow_reports' => $this->bool($req->allow_reports),
            'allow_emails' => $this->bool($req->allow_emails),
            'allow_logins' => $this->bool($req->allow_logins),
            'allow_vendors' => $this->bool($req->allow_vendors),
            'allow_clients' => $this->bool($req->allow_clients),
            'allow_deposits' => $this->bool($req->allow_deposits),
            'allow_withdraws' => $this->bool($req->allow_withdraws),
            'allow_payments' => $this->bool($req->allow_payments),
            'allow_pay_later' => $this->bool($req->allow_pay_later),
            'running' => $this->bool($req->running),
        ];

        $setting->update($data);
        $this->report($req, 'setting', 0, 'update', 'admin');

        return $this->success();

    }
    public function delete ( Request $req ) {

        $table = trim(strtolower($req->item));

        if ( $table == 'categories' ) { Category::query()->delete(); }
        if ( $table == 'products' ) { Product::query()->delete(); }
        if ( $table == 'coupons' ) { Coupon::query()->delete(); }
        if ( $table == 'orders' ) { Order::query()->delete(); }
        if ( $table == 'reviews' ) { Review::query()->delete(); }
        if ( $table == 'contacts' ) { Contact::query()->delete(); }
        if ( $table == 'blogs' ) { Blog::query()->delete(); }
        if ( $table == 'comments' ) { Comment::query()->delete(); }
        if ( $table == 'replies' ) { Reply::query()->delete(); }
        if ( $table == 'reports' ) { Report::query()->delete(); }
        if ( $table == 'mails' ) { Mail::query()->delete(); }
        if ( $table == 'messages' ) { Relation::query()->delete(); Message::query()->delete(); }
        if ( $table == 'clients' ) { User::where('role', 3)->delete(); }
        if ( $table == 'vendors' ) { User::where('role', 2)->delete(); }
        if ( $table == 'admins' ) { User::where('role', 1)->where('supervisor', false)->delete(); }
        if ( $table == 'supervisors' ) { User::where('role', 1)->where('super', false)->where('supervisor', true)->delete(); }

        $this->report($req, $table, 0, 'delete', 'admin');

        return $this->success();

    }

}
