<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Reply;
use App\Models\Review;
use App\Models\Payment;
use App\Models\Reset;

class ReportResource extends JsonResource {

    public function get_item () {

        $item = null;

        if ( $this->table === 'category' ) $item = CategoryResource::make( Category::withTrashed()->find($this->column) );
        if ( $this->table === 'product' ) $item = ProductResource::make( Product::withTrashed()->find($this->column) );
        if ( $this->table === 'coupon' ) $item = CouponResource::make( Coupon::withTrashed()->find($this->column) );
        if ( $this->table === 'order' ) $item = OrderResource::make( Order::withTrashed()->find($this->column) );
        if ( $this->table === 'review' ) $item = ReviewResource::make( Review::withTrashed()->find($this->column) );
        if ( $this->table === 'blog' ) $item = BlogResource::make( Blog::withTrashed()->find($this->column) );
        if ( $this->table === 'comment' ) $item = CommentResource::make( Comment::withTrashed()->find($this->column) );
        if ( $this->table === 'reply' ) $item = ReplyResource::make( Reply::withTrashed()->find($this->column) );
        if ( $this->table === 'admin' ) $item = UserResource::make( User::withTrashed()->find($this->column) );
        if ( $this->table === 'vendor' ) $item = UserResource::make( User::withTrashed()->find($this->column) );
        if ( $this->table === 'client' ) $item = UserResource::make( User::withTrashed()->find($this->column) );
        if ( $this->table === 'contact' ) $item = ContactResource::make( Contact::withTrashed()->find($this->column) );
        if ( $this->table === 'payment' ) $item = PaymentResource::make( Payment::withTrashed()->find($this->column) );
        if ( $this->table === 'reset' ) $item = ResetResource::make( Reset::withTrashed()->find($this->column) );

        return $item;

    }
    public function toArray ( Request $req ) {

        return [
            'id' => $this->id,
            'table' => $this->table,
            'column' => $this->column,
            'process' => $this->process,
            'ip' => $this->ip,
            'agent' => $this->agent,
            'location' => $this->location,
            'amount' => $this->amount,
            'price' => $this->price,
            'paid' => $this->paid,
            'status' => $this->status,
            'active' => $this->active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'info' => ['process' => $this->process, 'table' => $this->table],
            'user' => UserResource::make( $this->admin ?? $this->vendor ?? $this->client ),
            'item' => $this->get_item(),
        ];

    }

}
