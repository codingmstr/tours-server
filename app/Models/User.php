<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {

    use HasFactory, SoftDeletes, HasApiTokens;

    protected $fillable = [
        'role',
        'admin_id',
        'vendor_id',
        'name',
        'email',
        'phone',
        'description',
        'company',
        'image',
        'password',
        'language',
        'country',
        'city',
        'street',
        'location',
        'postal',
        'longitude',
        'latitude',
        'currency',
        'gender',
        'birth_date',
        'withdraw_balance',
        'pending_balance',
        'buy_balance',
        'withdraws',
        'deposits',
        'earned_points',
        'points',
        'age',
        'ip',
        'agent',
        'notes',
        'days',
        'times',
        'email_code',
        'phone_code',
        'front_id_photo',
        'back_id_photo',
        'supervisor',
        'allow_categories',
        'allow_products',
        'allow_coupons',
        'allow_orders',
        'allow_blogs',
        'allow_reports',
        'allow_contacts',
        'allow_clients',
        'allow_vendors',
        'allow_clients_wallet',
        'allow_vendors_wallet',
        'allow_statistics',
        'allow_messages',
        'allow_mails',
        'allow_reviews',
        'allow_likes',
        'allow_dislikes',
        'allow_comments',
        'allow_replies',
        'allow_login',
        'activate_email',
        'activate_phone',
        'activate_identity',
        'premium',
        'available',
        'active',
        'login_at',
        'remember_token',
    ];
    protected $hidden = [
        'password',
        'remember_token'
    ];
    protected $casts = [
        'login_at' => 'datetime',
        'birth_date' => 'datetime',
        'days' => 'json',
        'times' => 'json',
    ];

    public function admin () {

        return $this->belongsTo(User::class, 'admin_id');

    }
    public function vendor () {

        return $this->belongsTo(User::class, 'vendor');

    }
    public function orders () {

        return $this->hasMany(Order::class, 'client_id');

    }
    public function reviews () {

        return $this->hasMany(Review::class, 'client_id');

    }
    public function comments () {

        return $this->hasMany(Comment::class, 'client_id');

    }
    public function replies () {

        return $this->hasMany(Reply::class, 'client_id');

    }
    public function vendor_products () {

        return $this->hasMany(Product::class, 'vendor_id');

    }
    public function vendor_orders () {

        return $this->hasMany(Order::class, 'vendor_id');

    }
    public function vendor_reviews () {

        return $this->hasMany(Review::class, 'vendor_id');

    }

}
