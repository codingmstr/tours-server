<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Payments\Paymob;

class PaymobController extends Controller {

    protected $paymob;

    public function __construct () {

        $this->paymob = new Paymob();

    }
    public function checkout ( Request $req ) {

        $order = [
            'id' => uniqid(),
            'amount' => 500,
            'currency' => 'EGP',
            'customer' => [
                'first_name' => 'Coding',
                'last_name' => 'Master',
                'email' => 'codingmaster009@gmail.com',
                'phone_number' => '01221083507',
                'country' => 'EGY',
                'city' => 'Banha',
                'state' => 'Qalubia',
                'street' => 'city star',
                'postal_code' => '13511',
                'floor' => 'NA',
                'building' => 'NA',
                'apartment' => 'NA',
                'shipping_method' => 'NA',
            ],
        ];

        $url = $this->paymob->pay_link($order);

        return redirect()->away($url);

    }
    public function callback ( Request $req ) {

        $success = $this->paymob->callback( $req->all() );

        if ( !$success ) return redirect()->route('payments.paymob.cancel');

        session()->flash('message-success', 'Paymob payment done successfully ✔');
        return redirect()->route('payments');

    }
    public function cancel ( Request $req ) {

        session()->flash('message-failed', 'Paymob payment failed !');
        return redirect()->route('payments');

    }
    public function process ( Request $req ) {

        ////   POST method callback   ////

        $sucess = $this->paymob->process( $req->all() );

        return response();

    }

}
