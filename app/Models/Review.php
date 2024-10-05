<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Review extends Model {

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admin_id',
        'vendor_id',
        'client_id',
        'product_id',
        'order_id',
        'content',
        'rate',
        'allow',
        'active',
    ];

    public function admin () {

        return $this->belongsTo(User::class, 'admin_id');

    }
    public function vendor () {

        return $this->belongsTo(User::class, 'vendor_id');

    }
    public function client () {

        return $this->belongsTo(User::class, 'client_id');

    }
    public function product () {

        return $this->belongsTo(Product::class);

    }
    public function order () {

        return $this->belongsTo(Order::class);

    }

}
