<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model {

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admin_id',
        'vendor_id',
        'client_id',
        'category_id',
        'product_id',
        'name',
        'discount',
        'notes',
        'allow_orders',
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
    public function category () {

        return $this->belongsTo(Category::class);

    }
    public function product () {

        return $this->belongsTo(Product::class);

    }
    public function orders () {

        return $this->hasMany(Order::class);

    }

}
