<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\ClientResource;
use App\Models\User;
use App\Models\File;
use App\Models\Review;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Comment;
use App\Models\Reply;

class ClientController extends Controller {

    public function statistics ( $id ) {

        $orders = $this->charts( Order::where('client_id', $id) );
        $coupons = $this->charts( Coupon::where('client_id', $id) );
        $reviews = $this->charts( Review::where('client_id', $id) );
        $comments = $this->charts( Comment::where('client_id', $id) );
        $replies = $this->charts( Reply::where('client_id', $id) );

        return ['orders' => $orders, 'reviews' => $reviews, 'coupons' => $coupons, 'comments' => $comments, 'replies' => $replies];

    }
    public function index ( Request $req ) {

        $data = $this->paginate( User::where('role', 3), $req );
        $items = ClientResource::collection( $data['items'] );
        return $this->success(['items' => $items, 'total' => $data['total']]);

    }
    public function show ( Request $req, User $user ) {

        if ( $user->role != 3 ) return $this->failed(['client' => 'not exists']);
        $item = ClientResource::make( $user );
        return $this->success(['item' => $item, 'statistics' => $this->statistics($user->id)]);

    }
    public function store ( Request $req ) {

        return $this->create_user($req, 3);

    }
    public function update ( Request $req, User $user ) {

        if ( $user->role != 3 ) return $this->failed(['client' => 'not exists']);
        return $this->update_user($req, $user);

    }
    public function delete ( Request $req, User $user ) {

        if ( $user->role != 3 ) return $this->failed(['client' => 'not exists']);
        $user->delete();
        $this->report($req, 'client', $user->id, 'delete', 'admin');
        return $this->success();

    }
    public function delete_group ( Request $req ) {

        foreach ( $this->parse($req->ids) as $id ) {
            User::where('id', $id)->where('role', 3)->delete();
            $this->report($req, 'client', $id, 'delete', 'admin');
        }

        return $this->success();

    }

}
