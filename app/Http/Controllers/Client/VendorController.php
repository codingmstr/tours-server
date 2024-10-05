<?php

namespace App\Http\Controllers\client;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;

class VendorController extends Controller {

    public function index ( Request $req, User $user ) {

        $vendor = UserResource::make( $user );
        return $this->success(['vendor' => $vendor]);

    }
    public function products ( Request $req, User $user ) {

        $products = Product::where('vendor_id', $user->id)->where('active', true)->where('allow', true);
        $data = $this->paginate( $products, $req );
        $items = ProductResource::collection( $data['items'] );
        return $this->success(['items' => $items, 'total'=> $data['total']]);

    }
    public function reviews ( Request $req, User $user ) {

        $products = Product::where('vendor_id', $user->id)->where('active', true)->where('allow', true)->pluck('id');
        $reviews = Review::whereIn('product_id', $products)->where('active', true)->where('allow', true);
        $data = $this->paginate( $reviews, $req );
        $items = ReviewResource::collection( $data['items'] );
        return $this->success(['items' => $items, 'total'=> $data['total']]);

    }

}
