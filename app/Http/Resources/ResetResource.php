<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResetResource extends JsonResource {

    public function toArray ( Request $req ) {

        return [
            'id' => $this->id,
            'token' => $this->token,
            'user_id' => $this->user_id,
            'user' => UserResource::make( $this->user ),
        ];

    }
}
