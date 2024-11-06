<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\CouponResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\UserResource;
use App\Models\Coupon;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;

class CouponController extends Controller {

    public function systems () {

        $categories = Category::where('active', true)->where('allow_coupons', true)->get();
        $categories = CategoryResource::collection( $categories );

        $products = Product::where('active', true)->where('allow_coupons', true)->get();
        $products = ProductResource::collection( $products );

        $vendors = User::where('role', '2')->where('active', true)->where('allow_coupons', true)->get();
        $vendors = UserResource::collection( $vendors );

        $clients = User::where('role', '3')->where('active', true)->where('allow_coupons', true)->get();
        $clients = UserResource::collection( $clients );

        return ['categories' => $categories, 'products' => $products, 'vendors' => $vendors, 'clients' => $clients];

    }
    public function default ( Request $req ) {
        
        return $this->success(self::systems());

    }
    public function index ( Request $req ) {

        $data = $this->paginate( Coupon::query(), $req );
        $items = CouponResource::collection( $data['items'] );
        return $this->success(['items' => $items, 'total'=> $data['total']]);

    }
    public function show ( Request $req, Coupon $coupon ) {

        $item = CouponResource::make( $coupon );
        return $this->success(['item' => $item] + self::systems());

    }
    public function store ( Request $req ) {

        if ( Coupon::where('name', $req->name)->exists() ) return $this->failed(['name' => 'exists']);
        $product = Product::find($req->product_id);

        $data = [
            'admin_id' => $this->user()->id,
            'category_id' => $this->integer($req->category_id),
            'product_id' => $this->integer($req->product_id),
            'vendor_id' => $this->integer($req->vendor_id) ? $this->integer($req->vendor_id) : $product?->vendor_id,
            'client_id' => $this->integer($req->client_id),
            'name' => $this->string($req->name),
            'discount' => $this->float($req->discount),
            'notes' => $this->string($req->notes),
            'allow_orders' => $this->bool($req->allow_orders),
            'allow' => $this->bool($req->allow),
            'active' => $this->bool($req->active),
        ];

        $coupon = Coupon::create($data);
        $this->report($req, 'coupon', $coupon->id, 'add', 'admin');
        return $this->success();

    }
    public function update ( Request $req, Coupon $coupon ) {

        if ( Coupon::where('name', $req->name)->where('id', '!=', $coupon->id)->exists() ) return $this->failed(['name' => 'exists']);
        $product = Product::find($req->product_id);

        $data = [
            'category_id' => $this->integer($req->category_id),
            'product_id' => $this->integer($req->product_id),
            'vendor_id' => $this->integer($req->vendor_id) ? $this->integer($req->vendor_id) : $product?->vendor_id,
            'client_id' => $this->integer($req->client_id),
            'name' => $this->string($req->name),
            'discount' => $this->float($req->discount),
            'notes' => $this->string($req->notes),
            'allow_orders' => $this->bool($req->allow_orders),
            'allow' => $this->bool($req->allow),
            'active' => $this->bool($req->active),
        ];

        $coupon->update($data);
        $this->report($req, 'coupon', $coupon->id, 'update', 'admin');
        return $this->success();

    }
    public function delete ( Request $req, Coupon $coupon ) {

        $coupon->delete();
        $this->report($req, 'coupon', $coupon->id, 'delete', 'admin');
        return $this->success();

    }
    public function delete_group ( Request $req ) {

        foreach ( $this->parse($req->ids) as $id ) {
            Coupon::find($id)?->delete();
            $this->report($req, 'coupon', $id, 'delete', 'admin');
        }
        
        return $this->success();

    }

}
