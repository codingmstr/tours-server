<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Mail extends Model {

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'title',
        'description',
        'content',
        'removed_sender',
        'star_sender',
        'important_sender',
        'archived_sender',
        'removed_receiver',
        'star_receiver',
        'important_receiver',
        'archived_receiver',
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

}
