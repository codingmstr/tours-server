<?php

namespace App\Http\Controllers\payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Transaction;
use App\Models\User;

class PaymobController extends Controller {

    protected $client;
    protected $apiKey;
    protected $integrationId;
    protected $iframeId;
    protected $hmacSecret;
    protected $apiBaseUrl;
    protected $callback_url;

    public function __construct () {

        $this->client = new Client();
        $this->integrationId = env('PAYMOB_INTEGRATION_ID');
        $this->iframeId = env('PAYMOB_IFRAME_ID');
        $this->apiKey = env('PAYMOB_API_KEY');
        $this->apiBaseUrl = env('PAYMOB_API_BASE_URL');
        $this->hmacSecret = env('PAYMOB_HMAC_SECRET');
        $this->callback_url = env('WEBHOOK_ENDPOINT') . 'paymob';

    }
    public function index ( Request $req ) {
                
        $response = $this->client->post($this->apiBaseUrl . 'auth/tokens', ['json' => ['api_key' => $this->apiKey]]);
        $token = json_decode($response->getBody()->getContents(), true)['token'];

        $data = [
            'auth_token' => $token,
            'merchant_order_id' => uniqid(),
            'amount_cents' => ceil( $this->exchange($req->amount, 'USD', 'EGP') * 100 ),
            'currency' => 'EGP',
        ];

        $response = $this->client->post($this->apiBaseUrl . 'ecommerce/orders', ['json' => $data]);
        $merchant= json_decode($response->getBody()->getContents(), true);

        $data = [
            'auth_token' => $token,
            'order_id' => $merchant['id'],
            'amount_cents' => $merchant['amount_cents'],
            'currency' => $merchant['currency'],
            'integration_id' => $this->integrationId,
            'expiration' => 3600,
            'billing_data' => [
                'first_name' => 'Coding',
                'last_name' => 'Master',
                'email' => 'codingmaster009@gmail.com',
                'phone_number' => '01221083507',
                'country' => 'EG',
                'city' => 'Cairo',
                'state' => 'Cairo',
                'street' => 'Talat Harb',
                'postal_code' => '13713',
                'floor' => 'NA',
                'building' => 'NA',
                'apartment' => 'NA',
                'shipping_method' => 'NA',
            ],
        ];
        
        $response = $this->client->post($this->apiBaseUrl . 'acceptance/payment_keys', ['json' => $data]);        
        $payment_token = json_decode($response->getBody()->getContents(), true)['token'];
        $url = "https://accept.paymobsolutions.com/api/acceptance/iframes/{$this->iframeId}?payment_token=$payment_token";

        Transaction::create([
            'user_id' => $this->user()->id,
            'transaction_id' => $merchant['id'],
            'amount' => $req->amount,
            'currency' => 'EGP',
            'payment' => 'paymob',
            'method' => 'card',
        ]);

        return $this->success(['url' => $url, 'transaction_id' => $merchant['id']]);

    }
    public function is_valid ( $req ) {

        $req = json_encode( $req->all() );
        $data = json_decode( $req );

        $amount_cents = $data->obj->amount_cents;
        $created_at = $data->obj->created_at;
        $currency = $data->obj->currency;
        $error_occured = $this->bool_string($data->obj->error_occured);
        $has_parent_transaction = $this->bool_string($data->obj->has_parent_transaction);
        $obj_id = $data->obj->id;
        $integration_id = $data->obj->integration_id;
        $is_3d_secure = $this->bool_string($data->obj->is_3d_secure);
        $is_auth = $this->bool_string($data->obj->is_auth);
        $is_capture = $this->bool_string($data->obj->is_capture);
        $is_refunded = $this->bool_string($data->obj->is_refunded);
        $is_standalone_payment = $this->bool_string($data->obj->is_standalone_payment);
        $is_voided = $this->bool_string($data->obj->is_voided);
        $order_id = $data->obj->order->id;
        $owner = $data->obj->owner;
        $pending = $this->bool_string($data->obj->pending);
        $source_data_pan = $data->obj->source_data->pan;
        $source_data_sub_type = $data->obj->source_data->sub_type;
        $source_data_type = $data->obj->source_data->type;
        $success = $this->bool_string($data->obj->success);

        $key = $amount_cents . $created_at . $currency . $error_occured . $has_parent_transaction . $obj_id .
                $integration_id . $is_3d_secure . $is_auth . $is_capture . $is_refunded . $is_standalone_payment . $is_voided .
                $order_id . $owner . $pending . $source_data_pan . $source_data_sub_type . $source_data_type . $success;

        $key = hash_hmac('SHA512', $key, $this->hmacSecret);
        return $key === $data->hmac;

    }
    public function callback ( Request $req ) {

        if ( !self::is_valid( $req ) ) return;

        // $data = ['transaction_id' => $req->obj['order']['id'], 'completed' => $req->success];
        $data = ['transaction_id' => $req->obj['order']['id'], 'completed' => true];
        $this->transaction( $data );

    }

}
