<?php

namespace App\Http\Controllers\client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller {

    public function process_query ( $text ) {

        $search = trim($text);

        $result = Product::where('id', $search)
            ->orWhere('name', $search)
            ->orWhere('phone', $search)
            ->orWhere('type', $search)
            ->orWhere('company', $search)
            ->orWhere('language', $search)
            ->orWhere('country', $search)
            ->orWhere('city', $search)
            ->orWhere('street', $search)
            ->orWhere('location', $search)
            ->orWhere('longitude', $search)
            ->orWhere('latitude', $search)
            ->orWhere('description', $search)
            ->orWhere('details', $search)
            ->orWhere('policy', $search)
            ->orWhereRaw("MATCH(name, phone, location, description, details) AGAINST(? IN BOOLEAN MODE)", [$text]);

        return $result;

    }
    public function search( $req ) {

        $query = Product::query();
        if ( $this->string($req->text) ) $query = self::process_query($this->string($req->text));
        if ( $this->integer($req->category) ) $query = $query->where('category_id', $this->integer($req->category));
        if ( $this->integer($req->vendor) ) $query = $query->where('vendor_id', $this->integer($req->vendor));
        if ( $this->integer($req->min_price) ) $query = $query->where('new_price', '>=', $this->integer($req->min_price));
        if ( $this->integer($req->max_price) ) $query = $query->where('new_price', '<=', $this->integer($req->max_price));
        if ( $req->date ) $query = $query->where('created_at', '>=', $req->date);
        if ( $req->rate ) $query = $query->whereHas('reviews', function ( $review ) use ( $req ) { $review->where('rate', '<=', $this->integer($req->rate) + .5); });

        if ( $req->sorted === 'latest' ) $query = $query->orderBy('id', 'desc');
        else if ( $req->sorted === 'oldest' ) $query = $query->orderBy('id', 'asc');
        else if ( $req->sorted === 'high_price' ) $query = $query->orderBy('new_price', 'desc');
        else if ( $req->sorted === 'low_price' ) $query = $query->orderBy('new_price', 'asc');
        else if ( $req->sorted === 'views' ) $query = $query->orderBy('views', 'desc');
        else if ( $req->sorted === 'order' ) $query = $query->withCount('orders')->orderBy('orders_count', 'desc');
        else if ( $req->sorted === 'review' ) $query = $query->withCount('reviews')->orderBy('reviews_count', 'desc');
        else if ( $req->sorted === 'rate' ) $query = $query->withAvg('reviews', 'rate')->orderBy('reviews_avg_rate', 'desc');
        
        $query = $query->where('active', true)
            ->where('allow', true)
            ->whereHas('vendor', function( $q ) { $q->where('active', true); })
            ->whereHas('category', function( $q ) { $q->where('active', true); });

        $total = $query->count();
        $items = $query->forPage($req->page ?? 1, $req->limit ?? 12)->get();
        return ['items' => $items, 'total' => $total];

    }
    public function index ( Request $req ) {

        $data = self::search($req);
        $items = ProductResource::collection( $data['items'] );
        return $this->success(['items' => $items, 'total'=> $data['total']]);

    }
    public function form ( Request $req ) {

        return $this->success();

    }
    
}
