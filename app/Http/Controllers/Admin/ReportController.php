<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;

class ReportController extends Controller {

    public function index ( Request $req ) {

        $data = $this->paginate( Report::query(), $req );
        $items = ReportResource::collection( $data['items'] );
      
        $tags = [
            'total' => $data['total'],
            'products' => Product::query()->count(),
            'orders' => Order::query()->count(),
            'users' => User::where('role', '!=', '1')->count(),
        ];
        
        return $this->success(['items' => $items, 'total'=> $data['total'], 'tags' => $tags]);

    }
    public function show ( Request $req, Report $report ) {

        $item = ReportResource::make( $report );
        return $this->success(['item' => $item]);

    }
    public function delete ( Request $req, Report $report ) {

        $report->delete();
        return $this->success();

    }
    public function delete_group ( Request $req ) {

        foreach ( $this->parse($req->ids) as $id ) Report::find($id)?->delete();
        return $this->success();

    }

}
