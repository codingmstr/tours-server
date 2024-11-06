<?php

namespace App\Http\Controllers\client\old;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\SettingResource;
use App\Http\Resources\ClientResource;
use App\Http\Resources\OrderResource;
use App\Models\Contact;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\Report;
use App\Models\Setting;

class HomeController extends Controller {

    public function recommended () {

        $history = Order::where('client_id', $this->user()?->id)
            ->latest()
            ->take(20)
            ->pluck('id')
            ->toArray();

        $viewed = Report::where('table', 'product')
            ->where('process', 'view')
            ->where('client_id', $this->user()?->id)
            ->latest()
            ->take(20)
            ->pluck('column')
            ->toArray();

        $popular = Product::where('active', true)
            ->where('allow', true)
            ->orderBy('views', 'desc')
            ->take(20)
            ->pluck('id')
            ->toArray();

        $productIds = array_unique(array_merge($history, $viewed, $popular));
        $products = Product::where('active', true)->where('allow', true)->whereIn('id', $productIds)->get();

        $products = $products->sortBy(function ($product) use ($history, $viewed, $popular) {
            if ( in_array($product->id, $history) ) return 1;
            if ( in_array($product->id, $viewed) ) return 2;
            if ( in_array($product->id, $popular) ) return 3;
            return 4;
        })->values()->take(20);

        if ( $products->count() < 20 ) {
            $recently = Product::where('active', true)->where('allow', true)->latest()->take(20)->get();
            $products = $products->merge($recently)->unique('id')->values()->take(20);
        }

        return ProductResource::collection( $products );

    }
    public function recently () {

        $products = Product::where('active', true)->where('allow', true)->latest()->take(20)->get();
        return ProductResource::collection( $products );

    }
    public function items () {

        $categories = Category::where('active', true)->latest()->get();
        return CategoryResource::collection( $categories );

    }
    public function index ( Request $req ) {

        $data = [
            'recommend_tours' => self::recommended(),
            'recent_tours' => self::recently(),
            'destinations' => self::items(),
            'settings' => SettingResource::make( Setting::find(1) ),
        ];

        return $this->success($data);

    }
    public function products ( Request $req, Category $category ) {

        if ( !$category->active ) return $this->failed();

        $items = Product::where('category_id', $category->id)->where('active', true)->where('allow', true)->latest()->get();
        $items = ProductResource::collection( $items );

        $data = [
            'destination' => CategoryResource::make( $category ),
            'tours' => $items,
            'recent_tours' => self::recently(),
            'settings' => SettingResource::make( Setting::find(1) ),
        ];

        return $this->success($data);

    }
    public function product ( Request $req, Product $product ) {

        if ( !$product->active || !$product->allow ) return $this->failed();
        $product = ProductResource::make( $product );

        $product->update(['views' => $product->views+1]);
        $this->report($req, 'product', $product->id, 'view', 'client');

        $data = [
            'tour' => $product,
            'recommend_tours' => self::recommended(),
            'settings' => SettingResource::make( Setting::find(1) ),
        ];

        return $this->success($data);

    }
    public function wishlist ( Request $req ) {

        $ids = $this->parse($req->ids);
        $items = Product::whereIn('id', $ids)->where('active', true)->where('allow', true)->latest()->get();
        $items = ProductResource::collection( $items );
        return $this->success(['tours' => $items]);

    }
    public function history ( Request $req ) {

        $items = Order::where('client_id', $this->user()->id)->where('active', true)->where('deleted', false)->latest()->get();
        $items = OrderResource::collection( $items );
        return $this->success(['bookings' => $items, 'recent_tours' => self::recently()]);

    }
    public function update_history ( Request $req ) {

        $ids = $this->parse($req->ids);
        Order::whereIn('id', $ids)->whereIn('status', ['pending', 'request'])->where('active', true)->update(['status' => $req->status]);
        return $this->success();
    
    }
    public function delete_history ( Request $req ) {

        $ids = $this->parse($req->ids);
        Order::whereIn('id', $ids)->where('active', true)->update(['deleted', true]);
        return $this->success();
    
    }
    public function account ( Request $req ) {

        $data = ['user' => ClientResource::make( $this->user() )];
        return $this->success($data);

    }
    public function update_account ( Request $req ) {

        $data = [
            'name' => $this->string($req->name),
            'email' => $this->string($req->email),
            'age' => $this->float($req->age),
            'phone' => $this->string($req->phone),
            'company' => $this->string($req->company),
            'country' => $this->string($req->country),
            'city' => $this->string($req->city),
            'postal' => $this->string($req->postal),
        ];

        $user = $this->user();
        $user->update($data);
        $user = ClientResource::make( $user );

        $this->report($req, 'account', 0, 'update', 'client');
        return $this->success(['user' => $user]);

    }
    public function reset_password ( Request $req ) {

        $user = $this->user();
        if ( !Hash::check($req->old_password, $user->password) ) return $this->failed(['password' => 'not correct']);
        $user->update(['password' => Hash::make($req->password)]);
        $this->report($req, 'password', 0, 'change', 'client');
        return $this->success();

    }
    public function search ( Request $req ) {

        $search_text = trim($req->text);

        $result = Product::where('id', $search_text)
            ->orWhere('name', $search_text)
            ->orWhere('phone', $search_text)
            ->orWhere('type', $search_text)
            ->orWhere('company', $search_text)
            ->orWhere('language', $search_text)
            ->orWhere('country', $search_text)
            ->orWhere('city', $search_text)
            ->orWhere('street', $search_text)
            ->orWhere('location', $search_text)
            ->orWhere('longitude', $search_text)
            ->orWhere('latitude', $search_text)
            ->orWhere('description', $search_text)
            ->orWhere('details', $search_text)
            ->orWhere('policy', $search_text)
            ->orWhereRaw("MATCH(name, phone, location, description, details) AGAINST(? IN BOOLEAN MODE)", [$search_text])
            ->where('created_at', '>', $req->date ?? '')
            ->whereNotNull('created_at')
            ->where('active', true)
            ->where('allow', true)
            ->latest()
            ->get();

        $data = [
            'tours' => ProductResource::collection( $result ),
            'settings' => SettingResource::make( Setting::find(1) ),
        ];

        return $this->success($data);

    }

}
