<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\AdminResource;
use App\Models\User;
use App\Models\File;

class AdminController extends Controller {

    public function index ( Request $req ) {

        $users = User::where('role', 1)->where('super', false)->where('id', '!=', $this->user()->id);
        if ( !$this->user()->super ) $users = $users->where('admin_id', $this->user()->id);

        $data = $this->paginate( $users, $req );
        $items = AdminResource::collection( $data['items'] );
        return $this->success(['items' => $items, 'total' => $data['total']]);

    }
    public function show ( Request $req, User $user ) {

        if ( $user->role != 1 || $user->super || $user->id == $this->user()->id ) return $this->failed(['admin' => 'not exists']);
        if ( !$this->user()->super && $user->admin_id != $this->user()->id ) return $this->failed(['admin' => 'not exists']);

        $item = AdminResource::make( $user );
        return $this->success(['item' => $item]);

    }
    public function store ( Request $req ) {

        return $this->create_user($req, 1);

    }
    public function update ( Request $req, User $user ) {

        if ( $user->role != 1 || $user->super || $user->id == $this->user()->id ) return $this->failed(['admin' => 'not exists']);
        if ( !$this->user()->super && $user->admin_id != $this->user()->id ) return $this->failed(['admin' => 'not exists']);
        return $this->update_user($req, $user);

    }
    public function delete ( Request $req, User $user ) {

        if ( $user->role != 1 || $user->super || $user->id == $this->user()->id ) return $this->failed(['admin' => 'not exists']);
        if ( !$this->user()->super && $user->admin_id != $this->user()->id ) return $this->failed(['admin' => 'not exists']);

        $user->delete();
        $this->report($req, 'admin', $user->id, 'delete', 'admin');
        return $this->success();

    }
    public function delete_group ( Request $req ) {

        foreach ( $this->parse($req->ids) as $id ) {

            $user = User::where('id', $id)->where('id', '!=', $this->user()->id)->where('super', false);
            if ( !$this->user()->super ) $user->where('admin_id', $this->user()->id);
            $user->delete();
            $this->report($req, 'admin', $id, 'delete', 'admin');

        }

        return $this->success();

    }

}
