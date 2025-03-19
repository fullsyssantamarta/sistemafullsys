<table class="combined-table">
    <thead>
        <tr>
            <th colspan="6">Información Básica</th>
            <th colspan="9">Detalles Financieros</th>
        </tr>
        <tr>
            <th>FECHA</th>
            <th>TIPO DOC</th>
            <th>PREFIJO</th>
            <th>IDENTIFICACIÓN</th>
            <th>NOMBRE</th>
            <th>DIRECCIÓN</th>
            <th>Total/Excento</th>
            <th>Descuento</th>
            <th>BASE</th>
            <th>Impuestos</th>
            @foreach($taxes as $tax)
                <th>{{ $tax->name }}</th>
            @endforeach
            <th>IVA Total</th>
            <th>Base + Impuesto</th>
            <th>Total pagar</th>
        </tr>
    </thead>
    <tbody>
        @php
            $total = 0;
            $net_total = 0;
            $total_exempt = 0;
            $total_discount = 0;
            $total_tax_base = 0;
            $total_tax_amount = 0;
            $tax_totals_by_type = [];
            foreach($taxes as $tax) {
                $tax_totals_by_type[$tax->id] = 0; 
            }
        @endphp

        @foreach($records as $value)
            @php
                $row = $value->getDataReportSalesBook();
                $customer = $value->person;
                
                // Identificar notas de crédito por el nombre del documento o por estado
                $is_credit_note = stripos($row['type_document_name'], 'crédit') !== false || 
                                ($value instanceof \App\Models\Tenant\DocumentPos && isset($row['state_type_id']) && $row['state_type_id'] === '11');
                
                $multiplier = $is_credit_note ? -1 : 1;
                
                $total += floatval(str_replace(',', '', $row['total'])) * $multiplier;
                $net_total += floatval(str_replace(',', '', $row['net_total'])) * $multiplier;
                $total_exempt += floatval(str_replace(',', '', $row['total_exempt'])) * $multiplier;
                $total_discount += floatval(str_replace(',', '', ($row['total_discount'] ?? 0))) * $multiplier;

                // Obtener nombres de impuestos
                $tax_names = collect($value->items)
                    ->pluck('tax.name')
                    ->unique()
                    ->implode(', ');

                // Calcular totales de impuestos por documento
                $tax_totals = [
                    'base' => 0,
                    'tax' => 0
                ];
                
                foreach($taxes as $tax) {
                    $item_values = $value->getItemValuesByTax($tax->id);
                    $tax_totals['base'] += floatval(str_replace(',', '', $item_values['taxable_amount'])) * $multiplier;
                    $tax_totals['tax'] += floatval(str_replace(',', '', $item_values['tax_amount'])) * $multiplier;
                }
                
                $total_tax_base += $tax_totals['base'];
                $total_tax_amount += $tax_totals['tax'];
            @endphp
            <tr class="{{ $is_credit_note ? 'credit-note' : '' }}">
                <td class="celda">{{ $row['date_of_issue'] }}</td>
                <td class="celda">{{ $row['type_document_name'] }}</td>
                <td class="celda">{{ $row['number_full'] }}</td>
                <td class="celda">{{ $customer ? $customer->number : ($row['customer_number'] ?? '') }}</td>
                <td class="celda">{{ $customer ? $customer->name : ($row['customer_name'] ?? '') }}</td>
                <td class="celda">{{ $customer ? $customer->address : ($row['customer_address'] ?? '') }}</td>
                <td class="celda text-right-td">{{ number_format(floatval(str_replace(',', '', $row['total_exempt'])) * $multiplier, 2, '.', '') }}</td>
                <td class="celda text-right-td">{{ number_format(floatval(str_replace(',', '', ($row['total_discount'] ?? 0))) * $multiplier, 2, '.', '') }}</td>
                <td class="celda text-right-td">{{ number_format(floatval(str_replace(',', '', $row['net_total'])) * $multiplier, 2, '.', '') }}</td>
                <td class="celda">{{ $tax_names }}</td>
                @foreach($taxes as $tax)
                    @php
                        $item_values = $value->getItemValuesByTax($tax->id);
                        $tax_amount = floatval(str_replace(',', '', $item_values['tax_amount'])) * $multiplier;
                        $tax_totals_by_type[$tax->id] += $tax_amount;
                    @endphp
                    <td class="celda text-right-td">{{ number_format($tax_amount, 2, '.', '') }}</td>
                @endforeach
                <td class="celda text-right-td">{{ number_format($tax_totals['tax'], 2, '.', '') }}</td>
                <td class="celda text-right-td">{{ number_format(floatval(str_replace(',', '', $row['net_total'])) * $multiplier + $tax_totals['tax'], 2, '.', '') }}</td>
                <td class="celda text-right-td">{{ number_format(floatval(str_replace(',', '', $row['total'])) * $multiplier, 2, '.', '') }}</td>
            </tr>
        @endforeach

        <tr>
            <th colspan="6" class="celda text-right-td">TOTALES</th>
            <th>{{ number_format($total_exempt, 2, '.', '') }}</th>
            <th>{{ number_format($total_discount, 2, '.', '') }}</th>
            <th>{{ number_format($net_total, 2, '.', '') }}</th>
            <th></th>
            @foreach($taxes as $tax)
                <th>{{ number_format($tax_totals_by_type[$tax->id], 2, '.', '') }}</th>
            @endforeach
            <th>{{ number_format($total_tax_amount, 2, '.', '') }}</th>
            <th>{{ number_format($total_tax_base + $total_tax_amount, 2, '.', '') }}</th>
            <th>{{ number_format($total, 2, '.', '') }}</th>
        </tr>
    </tbody>
</table>
