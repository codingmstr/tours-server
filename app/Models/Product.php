<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admin_id',
        'vendor_id',
        'category_id',
        'name',
        'company',
        'phone',
        'type',
        'language',
        'country',
        'city',
        'street',
        'location',
        'longitude',
        'latitude',
        'old_price',
        'new_price',
        'description',
        'details',
        'policy',
        'includes',
        'notes',
        'rate',
        'allow_reviews',
        'allow_orders',
        'allow_coupons',
        'allow',
        'active',
    ];
    protected $casts = [
        'includes' => 'json'
    ];

    public function admin () {

        return $this->belongsTo(User::class, 'admin_id');

    }
    public function vendor () {

        return $this->belongsTo(User::class, 'vendor_id');

    }
    public function category () {

        return $this->belongsTo(Category::class);

    }
    public function reviews () {

        return $this->hasMany(Review::class);

    }
    public function orders () {

        return $this->hasMany(Order::class);

    }
    public function coupons () {

        return $this->hasMany(Coupon::class);

    }
    public function messages () {

        return $this->hasMany(Message::class);

    }

}