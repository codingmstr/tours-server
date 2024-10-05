<?php

namespace App\Http\Controllers\client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ClientResource;
use Illuminate\Support\Facades\Hash;
use App\Models\File;

class AccountController extends Controller {

    public function index ( Request $req ) {

        $user = ClientResource::make( $this->user() );
        return $this->success(['user' => $user]);

    }
    public function save ( Request $req ) {

        $location = $this->get_location("{$req->street}, {$req->city}, {$req->country}");
        $user = $this->user();

        $validator = Validator::make($req->all(), [
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
        ]);
        if ( $validator->fails() ) {

            return $this->failed($validator->errors());

        }
        if ( $req->file('image_file') ) {

            $file_id = File::where('table', 'user')->where('column', $user->id)->first()?->id;
            $this->delete_files([$file_id], 'user');
            $this->upload_files([$req->file('image_file')], 'user', $user->id);

        }
        $data = [
            'name' => $this->string($req->name),
            'email' => $this->string($req->email),
            'age' => $this->float($req->age),
            'company' => $this->string($req->company),
            'phone' => $this->string($req->phone),
            'language' => $this->string($req->language),
            'country' => $this->string($req->country),
            'city' => $this->string($req->city),
            'street' => $this->string($req->street),
            'location' => "{$req->street}, {$req->city}, {$req->country}",
            'currency' => $this->string($req->currency),
            'longitude' => $this->string($req->longitude) ?? $location['longitude'],
            'latitude' => $this->string($req->latitude) ?? $location['latitude'],
        ];

        $user->update($data);
        $user = ClientResource::make( $user );
        $this->report($req, 'account', 0, 'update', 'client');
        return $this->success(['user' => $user]);

    }
    public function password ( Request $req ) {

        $user = $this->user();
        if ( !Hash::check($req->old_password, $user->password) ) return $this->failed(['password' => 'not correct']);
        $user->update(['password' => Hash::make($req->password)]);
        $this->report($req, 'password', 0, 'change', 'client');
        return $this->success();

    }

}
