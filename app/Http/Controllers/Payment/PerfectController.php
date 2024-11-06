<?php

namespace App\Http\Controllers\payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PaypalProvider;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class PerfectController extends Controller {

    protected $accountId;
    protected $accoutPass;
    protected $merchantId;
    protected $callback_url;

    public function __construct () {

        $this->accountId = env('PERFECT_ACCOUNT_ID');
        $this->accoutPass = env('PERFECT_ACCOUNT_PASS');
        $this->merchantId = env('PERFECT_MERCHANT_ID');
        $this->callback_url = env('WEBHOOK_ENDPOINT') . 'perfect';

    }
    public function index ( Request $req ) {

        $transaction_id = uniqid();

        $data = [
            'PAYEE_ACCOUNT' => $this->merchantId,
            'PAYMENT_ID' => $transaction_id,
            'PAYMENT_AMOUNT' => $req->amount,
            'STATUS_URL' => $this->callback_url,
            'PAYMENT_URL' => $req->redirect_url,
            'NOPAYMENT_URL' => $req->cancel_url,
            'PAYEE_NAME' => 'Microtech',
            'PAYMENT_UNITS' => 'USD',
            'PAYMENT_URL_METHOD' => 'GET',
            'NOPAYMENT_URL_METHOD' => 'GET',
            'SUGGESTED_MEMO' => 'Order Description',
        ];
        Transaction::create([
            'user_id' => $this->user()->id,
            'transaction_id' => $transaction_id,
            'amount' => $req->amount,
            'currency' => 'USD',
            'payment' => 'perfect',
            'method' => 'wallet',
        ]);

        return $this->success(['transaction_id' => $transaction_id, 'params' => $data]);

    }
    public function is_valid ( $req ) {
        
        return true;

    }
    public function callback ( Request $req ) {

        return Storage::disk('public')->put('file.txt', 'Perfectmoney Callback ...');

        if ( !self::is_valid( $req ) ) return;
        
        $data = ['transaction_id' => 1, 'completed' => true];
        $this->transaction( $data );

    }

}
