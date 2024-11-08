<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Category extends Model {

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admin_id',
        'vendor_id',
        'name',
        'company',
        'phone',
        'language',
        'country',
        'city',
        'street',
        'location',
        'description',
        'notes',
        'image',
        'allow_products',
        'allow_orders',
        'allow_coupons',
        'allow_reviews',
        'active',
    ];
    protected $casts = [
        'name' => 'json',
        'company' => 'json',
        'location' => 'json',
        'description' => 'json',
    ];

    public function admin () {

        return $this->belongsTo(User::class, 'admin_id');

    }
    public function vendor () {

        return $this->belongsTo(User::class, 'vendor_id');

    }
    public function products () {

        return $this->hasMany(Product::class);

    }
    public function coupons () {

        return $this->hasMany(Coupon::class);

    }

}
