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
        'twitter',
        'linkedin',
        'youtube',
        'tiktok',
        'support_name',
        'support_role',
        'support_phone',
        'support_image',
        'support_message_title',
        'support_message_body',
    ];

    protected $casts = [
        'enable_store_pickup' => 'boolean',
    ];
}
