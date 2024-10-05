<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Payments\Paypal;

class PaypalController extends Controller {

    protected $paypal;

    public function __construct () {

        $this->paypal = new Paypal();

    }
    public function checkout ( Request $req ) {

        $order = [
            'id' => uniqid(),
            'amount' => 500,
            'currency' => 'USD',
            'callback_url' => route("payments.paypal.callback"),
            'cancel_url' => route("payments.paypal.cancel"),
        ];

        $url = $this->paypal->pay_link($order);

        return redirect()->away($url);

    }
    public function callback ( Request $req ) {

        $success = $this->paypal->callback( $req->all() );
        if ( !$success ) return redirect()->route('payments.paypal.cancel');

        session()->flash('message-success', 'Paypal payment done successfully ✔');
        return redirect()->route('payments');

    }
    public function cancel ( Request $req ) {

        session()->flash('message-failed', 'Paypal payment failed !');
        return redirect()->route('payments');

    }

}
