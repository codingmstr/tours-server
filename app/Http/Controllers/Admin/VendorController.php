<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\VendorResource;
use App\Models\User;
use App\Models\File;
use App\Models\Product;
use App\Models\Order;
use App\Models\Coupon;

class VendorController extends Controller {

    public function statistics ( $id ) {

        $products = $this->charts( Product::where('vendor_id', $id) );
        $orders = $this->charts( Order::where('vendor_id', $id) );
        $coupons = $this->charts( Coupon::where('vendor_id', $id) );
        return ['products' => $products, 'orders' => $orders, 'coupons' => $coupons];

    }
    public function index ( Request $req ) {

        $data = $this->paginate( User::where('role', 2), $req );
        $items = VendorResource::collection( $data['items'] );
        return $this->success(['items' => $items, 'total' => $data['total']]);

    }
    public function show ( Request $req, User $user ) {

        if ( $user->role != 2 ) return $this->failed(['vendor' => 'not exists']);
        $item = VendorResource::make( $user );
        return $this->success(['item' => $item, 'statistics' => $this->statistics($user->id)]);

    }
    public function store ( Request $req ) {

        return $this->create_user($req, 2);

    }
    public function update ( Request $req, User $user ) {

        if ( $user->role != 2 ) return $this->failed(['vendor' => 'not exists']);
        return $this->update_user($req, $user);

    }
    public function status ( Request $req, User $user ) {

        if ( $user->role != 2 ) return $this->failed(['vendor' => 'not exists']);
        $user->update(['available' => $this->bool($req->available)]);
        $this->report($req, 'vendor', $user->id, 'update_status', 'admin', ['status' => $user->avilable ? 'available' : 'busy']);
        return $this->success();

    }
    public function delete ( Request $req, User $user ) {

        if ( $user->role != 2 ) return $this->failed(['vendor' => 'not exists']);
        $user->delete();
        $this->report($req, 'vendor', $user->id, 'delete', 'admin');
        return $this->success();

    }
    public function delete_group ( Request $req ) {

        foreach ( $this->parse($req->ids) as $id ) {
            User::where('id', $id)->where('role', 2)->delete();
            $this->report($req, 'vendor', $id, 'delete', 'admin');
        }

        return $this->success();

    }

}
