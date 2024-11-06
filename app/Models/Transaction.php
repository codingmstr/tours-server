<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'transaction_id',
        'type',
        'payment',
        'method',
        'currency',
        'amount',
        'description',
        'status',
        'active',
    ];

    public function user () {

        return $this->belongsTo(User::class);

    }

}
