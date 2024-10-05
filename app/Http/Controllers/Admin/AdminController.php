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

        $validator = Validator::make($req->all(), ['email' => ['required', 'email', 'unique:users'], 'password' => ['required']]);
        if ( $validator->fails() ) return $this->failed($validator->errors());
        
        $data = [
            'role' => 1,
            'admin_id' => $this->user()->id,
            'password' => Hash::make($req->password),
            'ip' => $req->ip(),
            'agent' => $req->userAgent(),
        ];

        $user = User::create($data + $this->user_table($req));
        $this->upload_files([$req->file('image_file')], 'user', $user->id);
        $this->report($req, 'admin', $user->id, 'add', 'admin');
        return $this->success();

    }
    public function update ( Request $req, User $user ) {

        if ( $user->role != 1 || $user->super || $user->id == $this->user()->id ) return $this->failed(['admin' => 'not exists']);
        if ( !$this->user()->super && $user->admin_id != $this->user()->id ) return $this->failed(['admin' => 'not exists']);

        $validator = Validator::make($req->all(), ['email' => ['required', 'email', 'unique:users,email,' . $user->id]]);
        if ( $validator->fails() ) return $this->failed($validator->errors());
        if ( $req->password ) $user->password = Hash::make($req->password);

        if ( $req->file('image_file') ) {
            $file_id = File::where('table', 'user')->where('column', $user->id)->first()?->id;
            $this->delete_files([$file_id], 'user');
            $this->upload_files([$req->file('image_file')], 'user', $user->id);
        }

        $user->update( $this->user_table($req) );
        $this->report($req, 'admin', $user->id, 'update', 'admin');
        return $this->success();

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
