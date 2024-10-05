<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Message extends Model {

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'product_id',
        'type',
        'content',
        'removed_sender',
        'removed_receiver',
        'star_sender',
        'star_receiver',
        'readen',
        'readen_at',
    ];
    protected $casts = [
        'readen_at' => 'datetime',
    ];

    public function sender () {

        return $this->belongsTo(User::class, 'sender_id');

    }
    public function receiver () {

        return $this->belongsTo(User::class, 'receiver_id');

    }
    public function product () {

        return $this->belongsTo(Product::class);

    }

}
