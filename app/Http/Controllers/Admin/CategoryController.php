<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\User;
use App\Models\File;

class CategoryController extends Controller {

    public function statistics ( $id ) {

        $products = $this->charts( Product::where('category_id', $id) );
        $coupons = $this->charts( Coupon::where('category_id', $id) );
        return ['products' => $products, 'coupons' => $coupons];

    }
    public function index ( Request $req ) {

        $data = $this->paginate( Category::query(), $req );
        $items = CategoryResource::collection( $data['items'] );
        return $this->success(['items' => $items, 'total'=> $data['total']]);

    }
    public function show ( Request $req, Category $category ) {

        $item = CategoryResource::make( $category );
        $data = ['item' => $item, 'statistics' => self::statistics($category->id)];
        return $this->success($data);

    }
    public function store ( Request $req ) {

        if ( Category::where('slug', $this->slug($req->name))->exists() ) {

            return $this->failed(['name' => 'exists']);

        }
        $data = [
            'admin_id' => $this->user()->id,
            'vendor_id' => $this->integer($req->vendor_id),
            'name' => $this->string($req->name),
            'slug' => $this->slug($req->name),
            'company' => $this->string($req->company),
            'phone' => $this->string($req->phone),
            'location' => $this->string($req->location),
            'description' => $this->string($req->description),
            'notes' => $this->string($req->notes),
            'allow_products' => $this->bool($req->allow_products),
            'allow_coupons' => $this->bool($req->allow_coupons),
            'allow_orders' => $this->bool($req->allow_orders),
            'allow_reviews' => $this->bool($req->allow_reviews),
            'active' => $this->bool($req->active),
        ];

        $category = Category::create($data);
        $this->upload_files([$req->file('image_file')], 'category', $category->id);
        $this->report($req, 'category', $category->id, 'add', 'admin');
        return $this->success();

    }
    public function update ( Request $req, Category $category ) {

        if ( Category::where('slug', $this->slug($req->name))->where('id', '!=', $category->id)->exists() ) {

            return $this->failed(['name' => 'exists']);

        }
        if ( $req->file('image_file') ) {

            $file_id = File::where('table', 'category')->where('column', $category->id)->first()?->id;
            $this->delete_files([$file_id], 'category');
            $this->upload_files([$req->file('image_file')], 'category', $category->id);

        }
        $data = [
            'vendor_id' => $this->integer($req->vendor_id),
            'name' => $this->string($req->name),
            'slug' => $this->slug($req->name),
            'company' => $this->string($req->company),
            'phone' => $this->string($req->phone),
            'location' => $this->string($req->location),
            'description' => $this->string($req->description),
            'notes' => $this->string($req->notes),
            'allow_products' => $this->bool($req->allow_products),
            'allow_coupons' => $this->bool($req->allow_coupons),
            'allow_orders' => $this->bool($req->allow_orders),
            'allow_reviews' => $this->bool($req->allow_reviews),
            'active' => $this->bool($req->active),
        ];

        $category->update($data);
        $this->report($req, 'category', $category->id, 'update', 'admin');
        return $this->success();

    }
    public function delete ( Request $req, Category $category ) {

        $category->delete();
        $this->report($req, 'category', $category->id, 'delete', 'admin');
        return $this->success();

    }
    public function delete_group ( Request $req ) {

        foreach ( $this->parse($req->ids) as $id ) {
            Category::find($id)?->delete();
            $this->report($req, 'category', $id, 'delete', 'admin');
        }

        return $this->success();

    }

}
