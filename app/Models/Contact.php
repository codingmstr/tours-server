<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model {

    use HasFactory, SoftDeletes;

    protected $fillable = [
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
        'ip',
        'agent',
        'content',
        'active',
    ];

}
