<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource {

    public function toArray ( Request $req ) {

        return [
            'id' => $this->id,
        ];

    }
}
