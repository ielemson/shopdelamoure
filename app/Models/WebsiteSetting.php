<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    protected $fillable = [
        'website_name',
        'logo',
        'favicon',
        'meta_title',
        'meta_description',
        'meta_keywords',

        'enable_store_pickup',

        'phone',
        'email',
        'address',

        'facebook',
        'instagram',
        'snapchat',
        'tiktok',

        'support_name',
        'support_role',
        'support_phone',
        'support_image',
        'support_message_title',
        'support_message_body',

        'delivery_provider',
        'shipping_markup_percentage',
        'terminal_environment',

        'dispatch_name',
        'dispatch_email',
        'dispatch_phone',
        'dispatch_line1',
        'dispatch_city',
        'dispatch_state',
        'dispatch_country',
        'dispatch_postal_code',
    ];

    protected $hidden = [
        'terminal_credentials',
    ];

    protected $casts = [
        'enable_store_pickup' => 'boolean',
        'shipping_markup_percentage' => 'decimal:2',
        'terminal_credentials' => 'encrypted:array',
    ];
}
