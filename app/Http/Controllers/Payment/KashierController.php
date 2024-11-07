<?php

namespace App\Http\Controllers\payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Transaction;
use App\Models\User;

class KashierController extends Controller {

    protected $client;
    protected $baseUrl;
    protected $merchantId;
    protected $apiKey;
    protected $secretKey;
    protected $callback_url;

    public function __construct () {

        $this->client = new Client();
        $this->baseUrl = env('KASHIER_API_URL');
        $this->merchantId = env('KASHIER_MERCHANT_ID');
        $this->apiKey = env('KASHIER_API_KEY');
        $this->secretKey = env('KASHIER_SECRET_KEY');
        $this->callback_url = env('WEBHOOK_ENDPOINT') . 'kashier';

    }
    public function index ( Request $req, $method=null ) {
        
        $transaction_id = uniqid();
        $orderId = $transaction_id;
        $amount = $this->exchange($req->amount, 'USD', 'EGP');
        $currency = 'EGP';
        $merchantId = $this->merchantId;
        $successUrl = $req->redirect_url;
        $failureUrl = $req->cancel_url;
        $webhookUrl = $this->callback_url;
        $allowedMethod = $method ?? 'card,bank_installments';
        $path = "/?payment=".$this->merchantId.".".$orderId.".".$amount.".".$currency;
        $hash = hash_hmac( 'sha256' , $path , $this->apiKey ,false);

        $paymentUrl = "https://checkout.kashier.io/?merchantId={$this->merchantId}&"
            . "orderId={$orderId}&"
            . "amount={$amount}&"
            . "currency={$currency}&"
            . "hash={$hash}&"
            . "mode=test&"
            . "metaData={}&"
            . "merchantRedirect={$successUrl}&"
            . "failureUrl={$failureUrl}&"
            . "serverWebhook={$webhookUrl}&"
            . "allowedMethods={$allowedMethod}&"
            . "failureRedirect=true&"
            . "redirectMethod=get&"
            . "brandColor=#46b8b0&"
            . "display=en";


        Transaction::create([
            'user_id' => $this->user()->id,
            'transaction_id' => $transaction_id,
            'amount' => $req->amount,
            'currency' => 'EGP',
            'payment' => 'kashier',
            'method' => $method ?? 'card',
            'description' => json_encode($req->all()),
        ]);

        return $this->success(['url' => $paymentUrl, 'transaction_id' => $transaction_id]);

    }
    public function wallet ( Request $req ) {
        
        return self::index($req, 'wallet');
    
    }
    public function is_valid ( $req ) {

        $rawPayload = $req->getContent();
        $jsonData = json_decode($rawPayload, true);
        $dataObj = $jsonData['data'];
        $event = $jsonData['event'];

        sort($dataObj['signatureKeys']);

        $headers = $req->headers->all();
        $headers = array_change_key_case($headers);
        $kashierSignature = $headers['x-kashier-signature'][0];
        $data = [];

        foreach ($dataObj['signatureKeys'] as $key) $data[$key] = $dataObj[$key];

        $queryString = http_build_query($data, '', '&', PHP_QUERY_RFC3986);
        $signature = hash_hmac('sha256', $queryString, $this->apiKey);

        if ( $signature === $kashierSignature ) return true;
        else return false;

    }
    public function callback ( Request $req ) {

        if ( !self::is_valid( $req ) ) return;

        $data = [
            'transaction_id' => $req->data['merchantOrderId'],
            'completed' => $req->data['status'] === 'SUCCESS'
        ];
        
        $this->transaction( $data );

    }

}
