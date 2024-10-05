<?php

namespace App\Http\Controllers\client;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ReviewResource;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Review;
use App\Models\Report;

class ProductController extends Controller {

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
            ->orderBy('views', 'desc')
            ->take(20)
            ->pluck('id')
            ->toArray();

        $productIds = array_unique(array_merge($history, $viewed, $popular));
        $products = Product::where('active', true)->whereIn('id', $productIds)->get();

        $products = $products->sortBy(function ($product) use ($history, $viewed, $popular) {
            if ( in_array($product->id, $history) ) return 1;
            if ( in_array($product->id, $viewed) ) return 2;
            if ( in_array($product->id, $popular) ) return 3;
            return 4;
        })->values()->take(20);

        if ( $products->count() < 20 ) {
            $recently = Product::where('active', true)->latest()->take(20)->get();
            $products = $products->merge($recently)->unique('id')->values()->take(20);
        }

        return ProductResource::collection( $products );

    }
    public function index ( Request $req, Product $product ) {

        if ( !$product->active || !$product->allow ) return $this->failed();
        $product = ProductResource::make( $product );

        $product->update(['views' => $product->views+1]);
        $this->report($req, 'product', $product->id, 'view', 'client');

        $data = ['product' => $product, 'recommended' => self::recommended()];
        return $this->success($data);

    }
    public function reviews ( Request $req, Product $product ) {

        $reviews = Review::where('product_id', $product->id)->where('active', true)->where('allow', true);
        $data = $this->paginate( $reviews, $req );
        $items = ReviewResource::collection( $data['items'] );
        return $this->success(['items' => $items, 'total'=> $data['total']]);

    }

}
