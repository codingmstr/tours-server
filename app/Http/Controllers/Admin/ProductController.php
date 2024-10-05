<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\VendorResource;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Order;
use App\Models\Review;
use App\Models\Coupon;

class ProductController extends Controller {

    public function statistics ( $id ) {

        $orders = $this->charts( Order::where('product_id', $id) );
        $reviews = $this->charts( Review::where('product_id', $id) );
        $coupons = $this->charts( Coupon::where('product_id', $id) );

        return ['orders' => $orders, 'reviews' => $reviews, 'coupons' => $coupons];

    }
    public function systems () {

        $categories = Category::where('active', true)->where('allow_products', true)->get();
        $categories = CategoryResource::collection( $categories );

        $vendors = User::where('role', '2')->where('active', true)->where('allow_products', true)->get();
        $vendors = VendorResource::collection( $vendors );

        return ['categories' => $categories, 'vendors' => $vendors];

    }
    public function default ( Request $req ) {
        
        return $this->success(self::systems());

    }
    public function index ( Request $req ) {

        $data = $this->paginate( Product::query(), $req );
        $items = ProductResource::collection( $data['items'] );

        $tags = [
            'total' => $data['total'],
            'orders' => Order::query()->count(),
            'reviews' => Review::query()->count(),
            'coupons' => Coupon::query()->count(),
        ];
        
        return $this->success(['items' => $items, 'total'=> $data['total'], 'tags' => $tags]);

    }
    public function show ( Request $req, Product $product ) {

        $item = ProductResource::make( $product );
        $data = ['item' => $item, 'statistics' => self::statistics($product->id)] + self::systems();
        return $this->success($data);

    }
    public function store ( Request $req ) {

        $location = $this->get_location("{$req->street}, {$req->city}, {$req->country}");

        $data = [
            'admin_id' => $this->user()->id,
            'vendor_id' => $this->integer($req->vendor_id),
            'category_id' => $this->integer($req->category_id),
            'name' => $this->string($req->name),
            'company' => $this->string($req->company),
            'phone' => $this->string($req->phone),
            'type' => $this->string($req->type),
            'language' => $this->string($req->language),
            'country' => $this->string($req->country),
            'city' => $this->string($req->city),
            'street' => $this->string($req->street),
            'location' => "{$req->street}, {$req->city}, {$req->country}",
            'longitude' => $location['longitude'],
            'latitude' => $location['latitude'],
            'old_price' => $this->float($req->old_price),
            'new_price' => $this->float($req->new_price),
            'description' => $this->string($req->description),
            'details' => $this->string($req->details),
            'policy' => $this->string($req->policy),
            'notes' => $this->string($req->notes),
            'includes' => $req->includes,
            'rate' => $this->float($req->rate),
            'allow_reviews' => $this->bool($req->allow_reviews),
            'allow_coupons' => $this->bool($req->allow_coupons),
            'allow_orders' => $this->bool($req->allow_orders),
            'allow' => $this->bool($req->allow),
            'active' => $this->bool($req->active),
        ];

        $product = Product::create($data);
        $this->upload_files( $req->allFiles(), 'product', $product->id );
        $this->report($req, 'product', $product->id, 'add', 'admin');
        return $this->success();

    }
    public function update ( Request $req, Product $product ) {

        $location = $this->get_location("{$req->street}, {$req->city}, {$req->country}");

        $data = [
            'category_id' => $this->integer($req->category_id),
            'vendor_id' => $this->integer($req->vendor_id),
            'name' => $this->string($req->name),
            'company' => $this->string($req->company),
            'phone' => $this->string($req->phone),
            'type' => $this->string($req->type),
            'language' => $this->string($req->language),
            'country' => $this->string($req->country),
            'city' => $this->string($req->city),
            'street' => $this->string($req->street),
            'location' => "{$req->street}, {$req->city}, {$req->country}",
            'longitude' => $location['longitude'],
            'latitude' => $location['latitude'],
            'old_price' => $this->float($req->old_price),
            'new_price' => $this->float($req->new_price),
            'description' => $this->string($req->description),
            'details' => $this->string($req->details),
            'policy' => $this->string($req->policy),
            'notes' => $this->string($req->notes),
            'includes' => $req->includes,
            'rate' => $this->float($req->rate),
            'allow_reviews' => $this->bool($req->allow_reviews),
            'allow_coupons' => $this->bool($req->allow_coupons),
            'allow_orders' => $this->bool($req->allow_orders),
            'allow' => $this->bool($req->allow),
            'active' => $this->bool($req->active),
        ];

        $product->update($data);
        $this->upload_files( $req->allFiles(), 'product', $product->id );
        $this->delete_files( $this->parse($req->deleted_files), 'product' );
        $this->report($req, 'product', $product->id, 'update', 'admin');
        return $this->success();

    }
    public function delete ( Request $req, Product $product ) {

        $product->delete();
        $this->report($req, 'product', $product->id, 'delete', 'admin');
        return $this->success();

    }
    public function delete_group ( Request $req ) {

        foreach ( $this->parse($req->ids) as $id ) {
            Product::find($id)?->delete();
            $this->report($req, 'product', $id, 'delete', 'admin');
        }
        
        return $this->success();

    }

}