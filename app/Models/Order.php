<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admin_id',
        'vendor_id',
        'client_id',
        'product_id',
        'coupon_id',
        'transaction_id',
        'persons',
        'name',
        'email',
        'address',
        'company',
        'phone',
        'language',
        'country',
        'city',
        'street',
        'location',
        'longitude',
        'latitude',
        'postal',
        'currency',
        'notes',
        'secret_key',
        'price',
        'coupon_discount',
        'coupon_code',
        'paid',
        'status',
        'active',
        'paid_at',
        'confirmed_at',
        'cancelled_at',
        'ordered_at',
        'deleted',
    ];
    protected $casts = [
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'ordered_at' => 'datetime',
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
    public function coupon () {

        return $this->belongsTo(Coupon::class);

    }
    public function transaction () {

        return $this->belongsTo(Transaction::class);

    }

}
