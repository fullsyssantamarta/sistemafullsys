<?php

namespace Modules\Factcolombia1\Models\Tenant;

use Illuminate\Database\Eloquent\SoftDeletes;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class ConfigurationPurchaseCoupon extends Model
{
    use UsesTenantConnection;

    protected $table = 'configuration_purchase_coupons';
    
    protected $fillable = [
        'title',
        'description',
        'minimum_purchase_amount',
        'establishment',
        'coupon_date',
        'status',
    ];

}
