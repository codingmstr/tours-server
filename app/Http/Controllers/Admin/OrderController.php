<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CouponResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Coupon;

class OrderController extends Controller {

    public function systems () {

        $clients = User::where('role', 3)->where('active', true)->where('allow_orders', true)->get();
        $clients = UserResource::collection( $clients );

        $products = Product::where('active', true)->where('allow_orders', true)->get();
        $products = ProductResource::collection( $products );

        $coupons = Coupon::where('active', true)->where('allow_orders', true)->get();
        $coupons = CouponResource::collection( $coupons );

        return ['products' => $products, 'clients' => $clients, 'coupons' => $coupons];

    }
    public function default ( Request $req ) {

        return $this->success(self::systems());

    }
    public function index ( Request $req ) {

        $data = $this->paginate( Order::query(), $req );
        $items = OrderResource::collection( $data['items'] );

        $tags = [
            'total' => $data['total'],
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'pending' => Order::where('status', 'pending')->count(),
        ];

        return $this->success(['items' => $items, 'total'=> $data['total'], 'tags' => $tags]);

    }
    public function show ( Request $req, Order $order ) {

        $item = OrderResource::make( $order );
        return $this->success(['item' => $item] + self::systems());

    }
    public function store ( Request $req ) {

        $product = Product::where('id', $req->product_id)->where('allow_orders', true)->where('active', true)->first();
        $client = User::where('role', 3)->where('id', $req->client_id)->where('active', true)->where('allow_orders', true)->first();
        $coupon = Coupon::where('id', $req->coupon_id)->where('active', true)->where('allow_orders', true)->first();

        if ( !$client ) return $this->failed(['client' => 'not exists']);
        if ( !$product ) return $this->failed(['product' => 'not exists']);

        $price = $product->new_price;
        $secret_key = $this->random_key();
        $coupon_discount = 0;
        $coupon_code = null;
        $paid_at = null;
        $confirmed_at = null;
        $cancelled_at = null;

        if ( $coupon ) {
            $coupon_code = $coupon->name;
            $coupon_discount = $coupon->discount;
            $price -= $price * $coupon->discount / 100;
        }
        if ( $this->bool($req->paid) ) {
            $paid_at = date('Y-m-d H:i:s');
        }
        if ( $req->status == 'confirmed' ) {
            $confirmed_at = date('Y-m-d H:i:s');
        }
        if ( $req->status == 'cancelled' ) {
            $cancelled_at = date('Y-m-d H:i:s');
        }
        while ( Order::where('secret_key', $secret_key)->exists() ) {
            $secret_key = $this->random_key();
        }

        $data = [
            'admin_id' => $this->user()->id,
            'vendor_id' => $this->integer($req->vendor_id),
            'client_id' => $this->integer($req->client_id),
            'product_id' => $this->integer($req->product_id),
            'coupon_id' => $this->integer($req->coupon_id),
            'name' => $this->string($req->name),
            'email' => $this->string($req->email),
            'address' => $this->string($req->address),
            'company' => $this->string($req->company),
            'phone' => $this->string($req->phone),
            'language' => $this->string($req->language),
            'country' => $this->string($req->country),
            'city' => $this->string($req->city),
            'street' => $this->string($req->street),
            'location' => $this->string($req->location),
            'notes' => $this->string($req->notes),
            'secret_key' => $secret_key,
            'price' => $price,
            'coupon_discount' => $coupon_discount,
            'coupon_code' => $coupon_code,
            'paid' => $this->bool($req->paid),
            'status' => $this->string($req->status ?? 'pending'),
            'active' => $this->bool($req->active),
            'paid_at' => $paid_at,
            'confirmed_at' => $confirmed_at,
            'cancelled_at' => $cancelled_at,
            'ordered_at' => $this->string($req->ordered_at),
        ];

        $order = Order::create($data);
        $this->report($req, 'order', $order->id, 'add', 'admin', ['price' => $order->price, 'paid' => $order->paid, 'status' => $order->status]);
        return $this->success();

    }
    public function update ( Request $req, Order $order ) {

        if ( $this->bool($req->paid) && !$order->paid_at ) {
            $order->paid_at = date('Y-m-d H:i:s');
        }
        if ( $req->status == 'confirmed' && !$order->confirmed_at ) {
            $order->confirmed_at = date('Y-m-d H:i:s');
        }
        if ( $req->status == 'cancelled' && !$order->cancelled_at ) {
            $order->cancelled_at = date('Y-m-d H:i:s');
        }

        $data = [
            'name' => $this->string($req->name),
            'email' => $this->string($req->email),
            'address' => $this->string($req->address),
            'company' => $this->string($req->company),
            'phone' => $this->string($req->phone),
            'language' => $this->string($req->language),
            'country' => $this->string($req->country),
            'city' => $this->string($req->city),
            'street' => $this->string($req->street),
            'location' => $this->string($req->location),
            'notes' => $this->string($req->notes),
            'paid' => $this->bool($req->paid),
            'ordered_at' => $this->string($req->ordered_at),
            'status' => $this->string($req->status),
            'active' => $this->bool($req->active),
        ];

        $order->update($data);
        $this->report($req, 'order', $order->id, 'update', 'admin', ['price' => $order->price, 'paid' => $order->paid, 'status' => $order->status]);
        return $this->success();

    }
    public function delete ( Request $req, Order $order ) {

        $this->report($req, 'order', $order->id, 'delete', 'admin', ['price' => $order->price, 'paid' => $order->paid, 'status' => $order->status]);
        $order->delete();
        return $this->success();

    }
    public function delete_group ( Request $req ) {

        foreach ( $this->parse($req->ids) as $id ) {
            $order = Order::find($id);
            if ( !$order ) continue;
            $this->report($req, 'order', $id, 'delete', 'admin', ['price' => $order->price, 'paid' => $order->paid, 'status' => $order->status]);
            $order->delete();
        }

        return $this->success();

    }

}
