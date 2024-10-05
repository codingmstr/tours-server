<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relation extends Model {

    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'removed_sender',
        'removed_receiver',
        'archived_sender',
        'archived_receiver',
    ];

    public function sender () {

        return $this->belongsTo(User::class, 'sender_id');

    }
    public function receiver () {

        return $this->belongsTo(User::class, 'receiver_id');

    }

}
