<?php

namespace Modules\Report\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ReportCommissionCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->transform(function($row, $key) {
            $total_transactions = $row->documents->count() + $row->sale_notes->count();
            $acum_sales = collect($row->documents)->sum('total') + collect($row->sale_notes)->sum('total');
            
            // Cálculo de comisión según el tipo
            $total_commission = 0;
            if($row->user_commission) {
                if($row->user_commission->type == 'percentage') {
                    // Si es porcentaje, calculamos el porcentaje del total de ventas
                    $total_commission = $acum_sales * ($row->user_commission->amount / 100);
                } else {
                    // Si es monto fijo, multiplicamos el amount por la cantidad de transacciones
                    $total_commission = $row->user_commission->amount * $total_transactions;
                }
            }

            return [
                'user_name' => $row->name,
                'total_transactions' => $total_transactions,
                'acum_sales' => number_format($acum_sales, 2),
                'total_commision' => number_format($total_commission, 2),
            ];
        });
    }
}
