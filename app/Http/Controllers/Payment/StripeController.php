<?php

namespace App\Http\Controllers\payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\PaymentIntent;

class StripeController extends Controller {

    protected $secretKey;
    protected $webhookSecret;
    protected $callback_url;

    public function __construct () {

        $this->secretKey = env('STRIPE_SECRET_KEY');
        $this->webhookSecret = env('STRIPE_WEBHOOK_SECRET');
        $this->callback_url = env('WEBHOOK_ENDPOINT') . 'stripe';

    }
    public function index ( Request $req ) {

        Stripe::setApiKey($this->secretKey);

        $payment = PaymentIntent::create([
            'amount' => $req->amount * 100,
            'currency' => 'usd',
            'payment_method_types' => ['card'],
        ]);
        Transaction::create([
            'user_id' => $this->user()->id,
            'transaction_id' => $payment->id,
            'amount' => $req->amount,
            'currency' => 'USD',
            'payment' => 'stripe',
            'method' => 'card',
        ]);

        return $this->success(['client' => $payment->client_secret, 'transaction_id' => $payment->id]);

    }
    public function is_valid ( $req ) {

        $payload = $req->getContent();
        $sig_header = $req->headers->get('Stripe-Signature');
        
        $event = Webhook::constructEvent($payload, $sig_header, $this->webhookSecret);
        if ( $event ) return true;

    }
    public function callback ( Request $req ) {

        if ( !self::is_valid( $req ) ) return;
        
        $data = [
            'transaction_id' => $req->data['object']['id'],
            'completed' => $req->data['object']['status'] === 'succeeded',
        ];

        $this->transaction( $data );

    }

}
