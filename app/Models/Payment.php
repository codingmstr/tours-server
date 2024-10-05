<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model {

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_id',
        'location',
        'ip',
        'agent',
        'secret',
        'price',
        'active',
    ];

}
