<?php

namespace App\Http\Controllers\payment;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Transaction;
use App\Models\User;

class VerifyController extends Controller {
  
    public function index ( Request $req ) {

        $transaction = Transaction::where('transaction_id', $req->transaction_id)
            ->where('user_id', $this->user()->id)
            ->where('status', '!=', 'pending')
            ->where('active', true)
            ->firstOrFail();

        $transaction = TransactionResource::make( $transaction );
        return $this->success(['transaction' => $transaction]);

    }
   
}
