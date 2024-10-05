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

        $validator = Validator::make($req->all(), ['email' => ['required', 'email', 'unique:users'], 'password' => ['required']]);
        if ( $validator->fails() ) return $this->failed($validator->errors());

        $data = [
            'role' => 3,
            'admin_id' => $this->user()->id,
            'password' => Hash::make($req->password),
            'ip' => $req->ip(),
            'agent' => $req->userAgent(),
        ];

        $user = User::create($data + $this->user_table($req));
        $this->upload_files([$req->file('image_file')], 'user', $user->id);
        $this->report($req, 'client', $user->id, 'add', 'admin');
        return $this->success();

    }
    public function update ( Request $req, User $user ) {

        if ( $user->role != 3 ) return $this->failed(['client' => 'not exists']);
        
        $validator = Validator::make($req->all(), ['email' => ['required', 'email', 'unique:users,email,' . $user->id]]);
        if ( $validator->fails() ) return $this->failed($validator->errors());
        if ( $req->password ) $user->password = Hash::make($req->password);

        if ( $req->file('image_file') ) {
            $file_id = File::where('table', 'user')->where('column', $user->id)->first()?->id;
            $this->delete_files([$file_id], 'user');
            $this->upload_files([$req->file('image_file')], 'user', $user->id);
        }
      
        $user->update( $this->user_table($req) );
        $this->report($req, 'client', $user->id, 'update', 'admin');
        return $this->success();

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
