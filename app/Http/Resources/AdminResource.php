<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource {

    public function toArray ( Request $req ) {

        return array_merge([
            'super' => $this->super,
            'supervisor' => $this->supervisor,
        ], (new UserResource($this))->toArray($req));

    }

}
