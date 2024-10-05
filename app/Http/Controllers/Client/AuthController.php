<?php

namespace App\Http\Controllers\client;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use App\Http\Resources\ClientResource;
use Illuminate\Support\Facades\Mail;
use App\Models\Reset;
use App\Mail\Recovery;

class AuthController extends Controller {

    public function register ( Request $req ) {

        $validator = Validator::make($req->all(), ['email' => ['required', 'email', 'unique:users'], 'password' => ['required']]);
        if ( $validator->fails() ) return $this->failed($validator->errors());

        $data = [
            'role' => 3,
            'name' => $this->string($req->name),
            'email' => $this->string($req->email),
            'phone' => $this->string($req->phone),
            'password' => Hash::make($req->password),
            'ip' => $req->ip(),
            'agent' => $req->userAgent(),
            'login_at' => $this->date(),
            'longitude' => $this->string($req->longitude),
            'latitude' => $this->string($req->latitude),
        ];

        $user = User::create($data);
        $token = $user->createToken($req->userAgent())->plainTextToken;
        $user = ClientResource::make($user);

        $this->report($req, '', 0, 'register', '', ['client_id' => $user->id]);
        return $this->success(['user' => $user, 'token' => $token]);

    }
    public function login ( Request $req ) {

        $user = User::where('email', $req->email)->first();

        if ( !$user ) {

            return $this->failed(['email' => 'invalid email']);

        }
        if ( !Hash::check($req->password, $user->password) ) {

            return $this->failed(['password' => 'invalid password']);

        }
        if ( $user->role != 3 || !$user->allow_login || !$user->active ) {

            return $this->failed(['permission' => 'access denied']);

        }

        $user->update(['login_at' => $this->date()]);
        $token = $user->createToken($req->userAgent())->plainTextToken;
        $user = ClientResource::make($user);

        $this->report($req, '', 0, 'login', '', ['client_id' => $user->id]);
        return $this->success(['user' => $user, 'token' => $token]);

    }
    public function recovery ( Request $req ) {

        $user = User::where('email', $req->email)->first();
        if ( !$user ) return $this->failed(['email' => 'invalid email']);

        $token = hash('sha256', now()->toDateTimeString() . Str::random(50));

        if ( Reset::where('user_id', $user->id)->exists() ) Reset::where('user_id', $user->id)->update(['token' => $token]);
        else Reset::create(['user_id' => $user->id, 'token' => $token]);

        Mail::to($user->email)->queue(new Recovery($user, $token));
        $this->report($req, '', 0, 'recovery', '', ['client_id' => $user->id]);
        return $this->success();

    }
    public function check ( Request $req, $token ) {

        if ( !Reset::where('token', $token)->exists() ) return $this->failed();
        return $this->success();

    }
    public function change ( Request $req, $token ) {

        $token = Reset::where('token', $token)->latest()->first();
        $user = User::find($token?->user_id);
        if ( !$token || !$user || !$req->password ) return $this->failed();

        $user->update(['password' => Hash::make($req->password)]);
        $token->delete();
        $this->report($req, '', 0, 'change_password', '', ['client_id' => $user->id]);
        return $this->success();

    }
    public function logout ( Request $req ) {

        $this->report($req, '', 0, 'logout', 'client');
        $this->user()->currentAccessToken()->delete();
        return $this->success();

    }

}
