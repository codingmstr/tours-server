<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Report extends Model {

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admin_id',
        'vendor_id',
        'client_id',
        'table',
        'column',
        'process',
        'ip',
        'agent',
        'location',
        'price',
        'amount',
        'paid',
        'status',
        'active',
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

}
