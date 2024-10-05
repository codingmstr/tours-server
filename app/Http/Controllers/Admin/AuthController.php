<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\AdminResource;

class AuthController extends Controller {

    public function login ( Request $req ) {

        $user = User::where('email', $req->email)->first();

        if ( !$user ) {

            return $this->failed(['email' => 'invalid email']);

        }
        if ( !Hash::check($req->password, $user->password) ) {

            return $this->failed(['password' => 'invalid password']);

        }
        if ( $user->role != 1 || !$user->allow_login || !$user->active ) {

            return $this->failed(['permission' => 'access denied']);

        }

        $user->update(['login_at' => $this->date()]);
        $token = $user->createToken($req->userAgent())->plainTextToken;
        $user = AdminResource::make($user);
        $this->report($req, '', 0, 'login', '', ['admin_id' => $user->id]);

        return $this->success(['user' => $user, 'token' => $token]);

    }
    public function unlock ( Request $req ) {

        if ( !Hash::check($req->password, $this->user()->password) ) {

            return $this->failed(['password' => 'invalid password']);

        }

        self::logout($req);
        $token = $this->user()->createToken($req->userAgent())->plainTextToken;
        $user = AdminResource::make($this->user());

        return $this->success(['user' => $user, 'token' => $token]);

    }
    public function logout ( Request $req ) {

        $this->report($req, '', 0, 'logout', 'admin');
        $this->user()->currentAccessToken()->delete();
        return $this->success();

    }

}
