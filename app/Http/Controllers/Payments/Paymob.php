<?php

namespace App\Http\Controllers\Payments;
use GuzzleHttp\Client;

class Paymob {

    protected $client;
    protected $apiKey;
    protected $integrationId;
    protected $iframeId;
    protected $hmacSecret;
    protected $apiBaseUrl;

    public function __construct () {

        $this->client = new Client();
        $this->integrationId = env('PAYMOB_INTEGRATION_ID');
        $this->iframeId = env('PAYMOB_IFRAME_ID');
        $this->apiKey = env('PAYMOB_API_KEY');
        $this->apiBaseUrl = env('PAYMOB_API_BASE_URL');
        $this->hmacSecret = env('PAYMOB_HMAC_SECRET');

    }
    public function check_hmac ( $data ) {

        $data = json_decode($data);

        $amount_cents = $data->obj->amount_cents;
        $created_at = $data->obj->created_at;
        $currency = $data->obj->currency;
        $error_occured = var_export($data->obj->error_occured, true);
        $has_parent_transaction = var_export($data->obj->has_parent_transaction, true);
        $obj_id = $data->obj->id;
        $integration_id = $data->obj->integration_id;
        $is_3d_secure = var_export($data->obj->is_3d_secure, true);
        $is_auth = var_export($data->obj->is_auth, true);
        $is_capture = var_export($data->obj->is_capture, true);
        $is_refunded = var_export($data->obj->is_refunded, true);
        $is_standalone_payment = var_export($data->obj->is_standalone_payment, true);
        $is_voided = var_export($data->obj->is_voided, true);
        $order_id = $data->obj->order->id;
        $owner = $data->obj->owner;
        $pending = var_export($data->obj->pending, true);
        $source_data_pan = $data->obj->source_data->pan;
        $source_data_sub_type = $data->obj->source_data->sub_type;
        $source_data_type = $data->obj->source_data->type;
        $success = var_export($data->obj->success, true);

        $key = $amount_cents . $created_at . $currency . $error_occured . $has_parent_transaction . $obj_id .
                $integration_id . $is_3d_secure . $is_auth . $is_capture . $is_refunded . $is_standalone_payment . $is_voided .
                $order_id . $owner . $pending . $source_data_pan . $source_data_sub_type . $source_data_type . $success;

        $key = hash_hmac('SHA512', $key, $this->hmacSecret);
        return $key === $data->hmac;

    }
    public function pay_link ( $order ) {

        $response = $this->client->post($this->apiBaseUrl . 'auth/tokens', [
            'json' => ['api_key' => $this->apiKey]
        ]);
        $token = json_decode($response->getBody()->getContents(), true)['token'];

        $response = $this->client->post($this->apiBaseUrl . 'ecommerce/orders', [
            'json' => [
                'auth_token' => $token,
                'delivery_needed' => false,
                'amount_cents' => $order['amount'] * 100,
                'currency' => $order['currency'],
                'merchant_order_id' => $order['id'],
                'items' => $order['items'] ?? [],
            ]
        ]);
        $merchant= json_decode($response->getBody()->getContents(), true);

        $response = $this->client->post($this->apiBaseUrl . 'acceptance/payment_keys', [
            'json' => [
                'auth_token' => $token,
                'order_id' => $merchant['id'],
                'amount_cents' => $merchant['amount_cents'],
                'currency' => $merchant['currency'],
                'billing_data' => $order['customer'],
                'expiration' => 3600,
                'integration_id' => $this->integrationId,
            ]
        ]);
        $payment_token = json_decode($response->getBody()->getContents(), true)['token'];

        $url = "https://accept.paymobsolutions.com/api/acceptance/iframes/{$this->iframeId}?payment_token=$payment_token";

        return $url;

    }
    public function callback ( $data ) {

        // check ...

        // $data['succcess']
        // $data['pending']
        // $data['order_id']
        // $data['amount_cents']
        // $data['transaction_id']

        return true;

    }
    public function process ( $data ) {

        $is_secure = self::check_hmac( json_encode( $data ) );

        if ( !$is_secure ) return false;

        // $data['succcess']
        // $data['pending']
        // $data['order_id']
        // $data['amount_cents']
        // $data['transaction_id']

        // after check hmac .. you must check order success

        return true;

    }

}
