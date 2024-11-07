<?php

namespace App\Http\Controllers\client\old;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Order;
use App\Models\Coupon;
use Illuminate\Support\Facades\Mail;
use App\Mail\Order as MailOrder;

class OrderController extends Controller {

    public function discount ( $product, $coupon, $readOnly=false, $adults=1 ) {
        
        if ( !$coupon ) return false;
        if ( !$product->allow_coupons ) return false;
        if ( $product->category && !$product->category?->allow_coupons ) return false;
        if ( $product->vendor && !$product->vendor?->allow_coupons ) return false;
        if ( $coupon->product_id && $coupon->product_id !== $product->id ) return false;
        if ( $coupon->category_id && $coupon->category_id !== $product->category_id ) return false;
        if ( $coupon->vendor_id && $coupon->vendor_id !== $product->vendor_id ) return false;
        if ( !$readOnly && !$this->user()->allow_coupons ) return false;
        if ( !$readOnly && $coupon->client_id && $coupon->client_id !== $this->user()->id ) return false;

        $price = $product->new_price * $adults;
        return $price - ( $price * $coupon->discount / 100 );

    }
    public function index ( Request $req, Product $product ) {

        if ( !$product->active || !$product->allow_orders ) return $this->failed();
        $product = ProductResource::make( $product );
        return $this->success(['product' => $product]);

    }
    public function coupon ( Request $req, Product $product ) {

        $coupon = Coupon::where('name', $req->coupon)->where('active', true)->where('allow_orders', true)->first();
        $price = self::discount( $product, $coupon, true, $this->integer($req->adults) );
        if ( !$price ) return $this->failed();
        return $this->success(['price' => $price, 'discount' => $coupon->discount]);

    }
    public function checkout ( Request $req, Product $product ) {

        if ( !$product->allow_orders ) return $this->failed();
        if ( $product->category && !$product->category?->allow_orders ) return $this->failed();
        if ( $product->vendor && !$product->vendor?->allow_orders ) return $this->failed();

        $coupon = Coupon::where('name', $req->coupon)->where('active', true)->where('allow_orders', true)->first();
        $price = self::discount( $product, $coupon, false, $this->integer($req->adults) );
        $price = $price ? $price : ( $product->new_price * $this->integer($req->adults) );
        $secret_key = $this->random_key();
        $paid = false;
        $paid_at = null;

        while ( Order::where('secret_key', $secret_key)->exists() ) {

            $secret_key = $this->random_key();

        }
        if ( $this->bool($req->pay_now) ) {

            if ( !$this->bool($req->paid_status) ) return $this->failed();
            if ( Transaction::where('transaction_id', $req->paid_secret)->exists() ) return $this->failed();

            Transaction::create([
                'user_id' => $this->user()->id,
                'transaction_id' => $req->paid_secret,
                'amount' => $req->paid_price,
                'currency' => 'USD',
                'payment' => 'stripe',
                'method' => 'card',
                'status' => 'successful',
            ]);

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
            'persons' => $this->integer($req->adults),
            'ordered_at' => $this->string($req->book_date) . ' ' . $this->string($req->book_time),
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

        Mail::to($this->user()->email)->queue(new MailOrder($this->user(), $order));
        return $this->success(['order' => OrderResource::make( $order )]);

    }

}
