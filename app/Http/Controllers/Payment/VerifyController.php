<?php

namespace App\Http\Controllers\payment;
use App\Http\Controllers\Controller;
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
            ->first();

        return $this->success(['transaction' => $transaction]);

    }
   
}
