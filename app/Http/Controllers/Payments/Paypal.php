<?php

namespace App\Http\Controllers\Payments;
use Srmklive\PayPal\Services\PayPal as PaypalProvider;

class Paypal {

    protected $provider;

    public function __construct () {

        $provider = new PaypalProvider;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $this->provider = $provider;

    }
    public function pay_link ( $order) {

        $response = $this->provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => $order['callback_url'],
                "cancel_url" => $order['cancel_url'],
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => $order['currency'],
                        "value" => $order['amount'],
                    ],
                ],
            ],
        ]);

        $url = $response['links'][1]['href'];

        return $url;

    }
    public function callback ( $data ) {

        $response = $this->provider->capturePaymentOrder( $data['token'] );

        if ( !isset($response['status']) || $response['status'] !== 'COMPLETED' ) return false;

        return true;

    }

}
