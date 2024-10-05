<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model {

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admin_id',
        'vendor_id',
        'title',
        'slug',
        'description',
        'content',
        'company',
        'phone',
        'language',
        'country',
        'city',
        'street',
        'location',
        'notes',
        'views',
        'likes',
        'dislikes',
        'allow_comments',
        'allow_replies',
        'allow_likes',
        'allow_dislikes',
        'allow',
        'active',
    ];

    public function admin () {
        
        return $this->belongsTo(User::class, 'admin_id');

    }
    public function vendor () {
        
        return $this->belongsTo(User::class, 'vendor_id');

    }
    public function comments () {

        return $this->hasMany(Comment::class);

    }
    public function replies () {

        return $this->hasMany(Reply::class);

    }

}
