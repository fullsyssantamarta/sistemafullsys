<?php

namespace Modules\Factcolombia1\Models\Tenant;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class CustomerPurchaseCoupon extends Model
{
    use UsesTenantConnection;

    protected $table = 'customer_purchase_coupons';
    
    protected $fillable = [
        'configuration_purchase_coupon_id',
        'document_id',
        'document_number',
        'customer_name',
        'customer_number',
        'customer_phone',
        'customer_email',
        'document_amount',
        'expiration_date',
        'status',
    ];
    
}
