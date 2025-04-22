<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Impuestos</title>
    </head>
    <body>
        <table style="width:100%; border-collapse: collapse;">
            <tr>
                <td colspan="17" style="height:35px; text-align: center; vertical-align: middle; font-size: 16px; font-weight: bold; padding: 10px; background-color: #0e3abd; color: white;">
                    <h1 style="font-size: 24px; margin: 0; padding-bottom: 10px;">Reporte Impuestos</h1>
                </td>
            </tr>
            <tr>
                <td colspan="17" style="height:100px; text-align: center; vertical-align: middle; font-size: 12px; font-weight: bold; padding: 10px; background-color: #0e3abd; color: white;">

                    <span style="font-size: 12px;">Empresa: {{$company->name}}</span><br>
                    <span style="font-size: 12px;">Fecha: {{date('Y-m-d')}}</span><br>
                    <span style="font-size: 12px;">N° Documento: {{$company->number}}</span><br>
                    <span style="font-size: 12px;">Establecimiento: {{$establishment->address}} - {{$establishment->city->name}}</span>
                </td>
            </tr>
            <!-- Aquí seguiría el resto de tu tabla -->
        </table>
                 
        <br>
        @if(!empty($records))

            @inject('reportTax', 'App\Services\ReportTaxService')
            <div class="">
                <div class=" ">
                    @php
                        $sale_total = 0;
                        $total_discount = 0;
                        $total_factura = 0;

                    @endphp
                    <table class="">
                        <thead>
                            <tr>
                                <th class="">#</th>
                                <th>Fecha emisión</th>
                                <th>Cliente</th>

                                <th>Documento</th>
                                <th>Base</th>
                                <th>Descuento</th>

                                @foreach($taxTitles as $tt)
                                    <th>{{ $tt->name}}({{ $tt->rate}})</th>
                                @endforeach

                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($records as $key => $value)
                                <tr>
                                    <td class="celda">{{$loop->iteration}}</td>
                                    <td class="celda">{{$value->created_at}}</td>
                                    <td class="celda">{{$value->customer->name}}</td>

                                    <td>
                                        <div>{{$value->type_document->name}}</div>
                                        <div>
                                            {{$value->prefix}}{{$value->number}}
                                            @if($value->type_document_id != 1)
                                                ({{$value->prefix}}{{$value->number}})
                                            @endif
                                        </div>
                                    </td>

                                    <td>$ {{$value->sale}}</td>
                                    <td>$ {{$value->total_discount}}</td>

                                    @foreach($taxTitles as $tax)
                                        <td >${{$reportTax->getTaxTotalBill($tax, $value->taxes)}}</td>
                                    @endforeach

                                    <td>$ {{$value->total}}</td>
                                </tr>

                                @php
                                    $sale_total += $value->sale;
                                    $total_discount += $value->total_discount;
                                    $total_factura += $value->total;
                                @endphp
                            @endforeach

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4"><strong>Totales:</strong></td>
                                <td><strong>${{ $sale_total }}</strong></td>
                                <td><strong>${{ $total_discount }}</strong></td>
                                @foreach($taxTitles as $tax)
                                    <td><strong>${{$reportTax->getTaxTotal($tax, $taxesAll)}}</strong></td>
                                @endforeach
                                <td><strong>${{ $total_factura }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @else
            <div>
                <p>No se encontraron registros.</p>
            </div>
        @endif
    </body>
</html>
