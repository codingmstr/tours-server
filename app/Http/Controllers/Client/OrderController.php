<?php

namespace App\Http\Controllers\client;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Coupon;

class OrderController extends Controller {

    public function discount ( $product, $coupon, $readOnly=false ) {
        
        if ( !$coupon ) return false;
        if ( !$product->allow_coupons ) return false;
        if ( $product->category && !$product->category?->allow_coupons ) return false;
        if ( $product->vendor && !$product->vendor?->allow_coupons ) return false;
        if ( $coupon->product_id && $coupon->product_id !== $product->id ) return false;
        if ( $coupon->category_id && $coupon->category_id !== $product->category_id ) return false;
        if ( $coupon->vendor_id && $coupon->vendor_id !== $product->vendor_id ) return false;
        if ( !$readOnly && !$this->user()->allow_coupons ) return false;
        if ( !$readOnly && $coupon->client_id && $coupon->client_id !== $this->user()->id ) return false;

        return $product->new_price - ( $product->new_price * $coupon->discount / 100 );

    }
    public function index ( Request $req, Product $product ) {

        if ( !$product->active || !$product->allow_orders ) return $this->failed();
        $product = ProductResource::make( $product );
        return $this->success(['product' => $product]);

    }
    public function coupon ( Request $req, Product $product ) {

        $coupon = Coupon::where('name', $req->coupon)->where('active', true)->where('allow_orders', true)->first();
        $price = self::discount( $product, $coupon, true );
        if ( !$price ) return $this->failed();
        return $this->success(['price' => $price, 'discount' => $coupon->discount]);

    }
    public function checkout ( Request $req, Product $product ) {

        if ( !$product->allow_orders ) return $this->failed();
        if ( $product->category && !$product->category?->allow_orders ) return $this->failed();
        if ( $product->vendor && !$product->vendor?->allow_orders ) return $this->failed();

        $coupon = Coupon::where('name', $req->coupon)->where('active', true)->where('allow_orders', true)->first();
        $price = self::discount( $product, $coupon ) ?? $product->new_price;
        $secret_key = $this->random_key();
        $paid = false;
        $paid_at = null;

        while ( Order::where('secret_key', $secret_key)->exists() ) {

            $secret_key = $this->random_key();

        }
        if ( $this->bool($req->pay_now) ) {

            $balance = $this->user()->balance;
            if ( $price > $balance ) return $this->failed(['balance' => 'not enouph']);
            $this->user()->update(['balance' => $balance - $price]);
            $paid = true;
            $paid_at = date('Y-m-d H:i:s');

        }
        $data = [
            'client_id' => $this->user()->id,
            'product_id' => $product->id,
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
            'price' => $price,
            'secret_key' => $secret_key,
            'coupon_id' => $coupon?->id ?? 0,
            'coupon_discount' => $coupon?->discount ?? 0,
            'coupon_code' => $coupon?->name,
            'paid' => $paid,
            'paid_at' => $paid_at,
            'status' => 'pending',
            'active' => true,
        ];

        $order = Order::create($data);
        $reports = ['price' => $order->price, 'paid' => $order->paid, 'status' => $order->status];
        $this->report($req, 'order', $order->id, 'add', 'client', $reports);
        return $this->success();

    }

}
