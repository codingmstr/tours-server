<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model {

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'email1',
        'phone',
        'phone1',
        'company',
        'code',
        'currency',
        'language',
        'country',
        'city',
        'street',
        'location',
        'theme',
        'facebook',
        'whatsapp',
        'telegram',
        'twitter',
        'youtube',
        'instagram',
        'linkedin',
        'balance',
        'profit',
        'income',
        'expenses',
        'withdraws',
        'deposits',
        'allow_mails',
        'allow_messages',
        'allow_notifications',
        'allow_categories',
        'allow_products',
        'allow_coupons',
        'allow_orders',
        'allow_blogs',
        'allow_comments',
        'allow_replies',
        'allow_reviews',
        'allow_contacts',
        'allow_reports',
        'allow_logins',
        'allow_vendors',
        'allow_clients',
        'allow_emails',
        'allow_deposits',
        'allow_withdraws',
        'allow_payments',
        'allow_pay_later',
        'running',
        'about',
        'terms',
        'policy',
        'services',
        'help',
    ];

}
