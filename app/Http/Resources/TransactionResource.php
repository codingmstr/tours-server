<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource {

    public function toArray ( Request $req ) {

       return [
            'id' => $this->id,
            'transaction_id' => $this->transaction_id,
            'type' => $this->type,
            'payment' => $this->payment,
            'method' => $this->method,
            'currency' => $this->currency,
            'amount' => $this->amount,
            'description' => $this->description,
            'status' => $this->status,
            'active' => $this->active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'user_id' => $this->user_id,
            'user' => UserResource::make( $this->user ),
            'order' => OrderResource::make( $this->order ),
        ];

    }

}
