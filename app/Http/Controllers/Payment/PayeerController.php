<?php

namespace App\Http\Controllers\payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PaypalProvider;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class PayeerController extends Controller {

    protected $secretKey;
    protected $merchantId;
    protected $currency;
    protected $callback_url;

    public function __construct () {

        $this->secretKey = '';
        $this->merchantId = '';
        $this->currency = '';
        $this->callback_url = env('WEBHOOK_ENDPOINT') . 'payeer';

    }
    public function index ( Request $req ) {

        $transaction_id = uniqid();
        $amount = $req->amount;
        $description = "Order payment";

        $data = [
            'm_shop' => $this->merchantId,
            'm_orderid' => $transaction_id,
            'm_amount' => number_format($amount, 2, '.', ''),
            'm_curr' => $this->currency,
            'm_desc' => base64_encode($description)
        ];

        $hash_string = implode(':', array_values($data)) . ':' . $this->secretKey;
        $data['m_sign'] = strtoupper(hash('sha256', $hash_string));

        Transaction::create([
            'user_id' => $this->user()->id,
            'transaction_id' => $transaction_id,
            'amount' => $req->amount,
            'currency' => 'USD',
            'payment' => 'payeer',
            'method' => 'wallet',
        ]);

        return $this->success(['url' => $data, 'transaction_id' => $transaction_id]);

    }
    public function is_valid ( $req ) {
        
        return true;

    }
    public function callback ( Request $req ) {

        return Storage::disk('public')->put('file.txt', 'Payeer Callback ...');

        if ( !self::is_valid( $req ) ) return;
        
        $data = ['transaction_id' => $req->id, 'completed' => $req->status === 'COMPLETED'];
        $this->transaction( $data );

    }

}
