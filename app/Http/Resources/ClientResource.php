<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource {

    public function toArray ( Request $req ) {

        return array_merge([
            'orders' => count($this->orders),
            'reviews' => count($this->reviews),
        ], (new UserResource($this))->toArray($req));

    }

}
