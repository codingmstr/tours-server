<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class File extends Model {

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'table',
        'column',
        'type',
        'name',
        'size',
        'url',
        'active',
    ];

}
