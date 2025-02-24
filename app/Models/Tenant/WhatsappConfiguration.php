<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class WhatsappConfiguration extends ModelTenant
{
    protected $table = 'whatsapp_configurations';
    
    protected $fillable = [
        'api_url',
        'api_token',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];
}
