<?php

namespace App\Http\Controllers\client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Contact;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\Report;

class HomeController extends Controller {

    public function based_location () {

        $products = Product::where('active', true)
            ->where('allow', true)
            ->where('country', $this->user()?->country)
            ->where('city', $this->user()?->city)
            ->latest()->take(20)
            ->get();

        $by_country = Product::where('active', true)
            ->where('allow', true)
            ->where('country', $this->user()?->country)
            ->latest()->take(20)
            ->get();

        $products = $products->merge($by_country)
            ->unique('id')
            ->values()
            ->take(20);

        if ( $products->count() < 20 ) {
            $recently = Product::where('active', true)->where('allow', true)->latest()->take(20)->get();
            $products = $products->merge($recently)->unique('id')->values()->take(20);
        }

        return ProductResource::collection( $products );

    }
    public function near_by ( $req ) {

        $radius = 10; // 10 km
        $lng = $this->string($req->longitude) ?? $this->user()?->longitude;
        $lat = $this->string($req->latitude) ?? $this->user()?->latitude;

        $products = DB::table('products')
            ->selectRaw("*, ( 6371 * acos( cos( radians(?) ) *
                    cos( radians( latitude ) )
                    * cos( radians( longitude ) - radians(?) )
                    + sin( radians(?) ) *
                    sin( radians( latitude ) ) ) ) AS distance", [$lat, $lng, $lat])
            ->having('distance', '<', $radius)
            ->orderBy('distance')
            ->where('active', true)
            ->where('allow', true)
            ->take(20)->get();

        if ( $products->count() < 20 ) {
            $recently = Product::where('active', true)->where('allow', true)->latest()->take(20)->get();
            $products = $products->merge($recently)->unique('id')->values()->take(20);
        }

        return ProductResource::collection( $products );
        
    }
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
            'categories' => self::items(),
            'recently' => self::recently(),
            'recommended' => self::recommended(),
            'based_location' => self::based_location(),
            'near_by' => self::near_by($req),
        ];

        return $this->success($data);

    }
    public function categories ( Request $req ) {

        $data = [
            'categories' => self::items(),
            'recently' => self::recently(),
        ];

        return $this->success($data);

    }
    public function products ( Request $req, Category $category ) {

        if ( !$category->active ) return $this->failed();

        $items = Product::where('category_id', $category->id)->where('active', true)->where('allow', true)->latest()->get();
        $items = ProductResource::collection( $items );

        $data = [
            'category' => CategoryResource::make( $category ),
            'products' => $items,
            'recently' => self::recently(),
            'recommended' => self::recommended(),
        ];

        return $this->success($data);

    }
    public function contact ( Request $req ) {

        $data = [
            'name' => $this->string($req->name),
            'email' => $this->string($req->email),
            'phone' => $this->string($req->phone),
            'address' => $this->string($req->address),
            'company' => $this->string($req->company),
            'language' => $this->string($req->language),
            'country' => $this->string($req->country),
            'city' => $this->string($req->city),
            'street' => $this->string($req->street),
            'location' => $this->string($req->longitude) . ', ' . $this->string($req->latitude),
            'postal' => $this->string($req->postal),
            'content' => $this->string($req->content),
            'active' => true,
            'ip' => $req->ip(),
            'agent' => $req->userAgent(),
        ];

        $contact->create($data);
        $this->report($req, 'contact', $contact->id, 'create', 'client');
        return $this->success();

    }

}
