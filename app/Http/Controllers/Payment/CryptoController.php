<?php

namespace App\Http\Controllers\payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CryptoController extends Controller {

    protected $client;
    protected $apiKey;
    protected $baseUrl;
    protected $merchantId;
    protected $callback_url;

    public function __construct () {

        $this->client = new Client();
        $this->baseUrl = 'https://api.cryptomus.com/v1';
        $this->apiKey = '';
        $this->merchantId = '656a7538-3602-49c1-887e-b170f5157a98';
        $this->callback_url = env('WEBHOOK_ENDPOINT') . 'crypto';

    }
    public function index ( Request $req ) {

        $response = $this->client->post("{$this->baseUrl}/payment", [
            'json' => [
                'order_id' => uniqid(),
                'amount' => $req->amount,
                'currency' => 'USD',
                'callback_url' => $this->callback_url,
                'success_url' => $req->redirect_url,
                'cancel_url' => $req->cancel_url,
            ],
            'headers' => [
                'sign' => $this->apiKey,
                'merchant' => $this->merchantId,
                'Content-Type' => 'application/json'
            ],
        ]);

        $response = json_decode($response->getBody()->getContents(), true);

        return $this->success(['url' => 'https://cryptomus.com', 'response' => $response]);

    }
    public function is_valid ( $req ) {
        
        return true;

    }
    public function callback ( Request $req ) {

        return true;

    }

}
