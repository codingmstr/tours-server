<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use App\Models\Setting;

class StatisticController extends Controller {

    public function index ( Request $req ) {

        $vendors = $this->charts( User::where('role', 2) );
        $clients = $this->charts( User::where('role', 3) );
        $categories = $this->charts( Category::query() );
        $products = $this->charts( Product::query() );
        $coupons = $this->charts( Coupon::query() );
        $orders = $this->charts( Order::query() );
        $pending_orders = $this->charts( Order::where('status', 'pending') );
        $confirmed_orders = $this->charts( Order::where('status', 'confirmed') );
        $cancelled_orders = $this->charts( Order::where('status', 'cancelled') );
        $reviews = $this->charts( Review::query() );

        $settings = Setting::where('id', 1)->first();
        
        $data = [
            'vendors' => $vendors,
            'clients' => $clients,
            'categories' => $categories,
            'products' => $products,
            'coupons' => $coupons,
            'orders' => $orders,
            'pending_orders' => $pending_orders,
            'confirmed_orders' => $confirmed_orders,
            'cancelled_orders' => $cancelled_orders,
            'reviews' => $reviews,
            'balance' => $settings->balance,
            'income' => $settings->income,
            'profit' => $settings->profit,
            'expenses' => $settings->expenses,
            'deposits' => $settings->deposits,
            'withdraws' => $settings->withdraws,
        ];

        return $this->success($data);

    }

}
