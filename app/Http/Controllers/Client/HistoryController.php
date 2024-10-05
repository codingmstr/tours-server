<?php

namespace App\Http\Controllers\client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Review;

class HistoryController extends Controller {

    public function index ( Request $req ) {

        $orders = Order::where('client_id', $this->user()->id)->where('active', true)->where('deleted', false);
        $data = $this->paginate( $orders, $req );
        $items = OrderResource::collection( $data['items'] );
        return $this->success(['items' => $items, 'total'=> $data['total']]);

    }
    public function show ( Request $req, Order $order ) {

        if ( !$order->active || $order->deleted ) return $this->failed();
        $item = OrderResource::make( $order );
        return $this->success(['item' => $item]);

    }
    public function update ( Request $req, Order $order ) {

        if ( !$order->active || $order->deleted ) return $this->failed();

        $data = [
            'name' => $this->string($req->name),
            'email' => $this->string($req->email),
            'address' => $this->string($req->address),
            'company' => $this->string($req->company),
            'phone' => $this->string($req->phone),
            'language' => $this->string($req->language),
            'country' => $this->string($req->country),
            'city' => $this->string($req->city),
            'street' => $this->string($req->street),
            'location' => $this->string($req->location),
            'notes' => $this->string($req->notes),
            'ordered_at' => $this->string($req->ordered_at),
        ];

        $order->update($data);
        $params = ['price' => $order->price, 'paid' => $order->paid, 'status' => $order->status];
        $this->report($req, 'order', $order->id, 'update', 'client', $params);

        if ( $this->string($req->status) == 'request' ) {
            $order->status = 'request';
            $this->report($req, 'order', $order->id, 'requested', 'client', $params);
        }

        return $this->success();

    }
    public function review ( Request $req, Order $order ) {

        if ( !$order->active || $order->deleted ) return $this->failed();

        $data = [
            'product_id' => $order->product_id,
            'order_id' => $order->id,
            'client_id' => $this->user()->id,
            'content' => $this->string($req->content),
            'rate' => $this->float($req->rate),
        ];

        $review = Review::create($data);
        $this->report($req, 'review', $review->id, 'add', 'client'); 
        return $this->success();

    }
    public function delete ( Request $req, Order $order ) {

        $order->update(['deleted' => true]);
        return $this->success();

    }
    public function delete_group ( Request $req ) {

        foreach ( $this->parse($req->ids) as $id ) Order::find($id)?->update(['deleted' => true]);
        return $this->success();

    }

}
