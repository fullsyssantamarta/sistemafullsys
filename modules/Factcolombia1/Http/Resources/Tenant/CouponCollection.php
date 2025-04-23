<?php

namespace Modules\Factcolombia1\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CouponCollection extends ResourceCollection
{
    public function toArray($request) {

        return $this->collection->transform(function($row, $key){
            return [
                'id' => $row->id,
                'title' => $row->title,
                'description' => $row->description,
                'minimum_purchase_amount' => $row->minimum_purchase_amount,
                'establishment' => $row->establishment,
                'coupon_date' => $row->coupon_date,
                'status' => (bool) $row->status,
            ];
        });
    }
}
