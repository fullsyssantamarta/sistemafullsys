<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
    </head>
    <body>
        <table style="width:100%; border-collapse: collapse;">
            <tr>
                <td colspan="17" style="height:35px; text-align: center; vertical-align: middle; font-size: 16px; font-weight: bold; padding: 10px; background-color: #0e3abd; color: white;">
                    <h1 style="font-size: 24px; margin: 0; padding-bottom: 10px;">Reporte Documentos</h1>
                </td>
            </tr>
            <tr>
                <td colspan="17" style="height:100px; text-align: center; vertical-align: middle; font-size: 12px; font-weight: bold; padding: 10px; background-color: #0e3abd; color: white;">

                    <span style="font-size: 12px;">Empresa: {{$company->name}}</span><br>
                    <span style="font-size: 12px;">Fecha: {{date('Y-m-d')}}</span><br>
                    <span style="font-size: 12px;">N° Documento: {{$company->number}}</span><br>
                    <span style="font-size: 12px;">Establecimiento: {{$establishment->address}} - {{$establishment->address}} - {{$establishment->country->name}} - {{$establishment->department->name}} - {{$establishment->city->name}}</span>
                </td>
            </tr>
            <!-- Aquí seguiría el resto de tu tabla -->
        </table>
        <br>
        @if(!empty($records))
            <div class="">
                <div class=" ">
                    <table class="">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="">Usuario/Vendedor</th>
                                <th>Tipo Doc</th>
                                <th>Número</th>
                                <th>Fecha emisión</th>
                                <th>Doc. Afectado</th>
                                <th>Cotización</th>
                                <th>Caso</th>
                                <th>Cliente</th>
                                <th>N° Documento</th>
                                <th>Estado</th>
                                <th class="">Moneda</th> 
                                <th>Subtotal</th>
                                <th class="">Descuento</th>                                
                                <th>Valor Impuesto</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>

                            @php
                                $sum_sale = 0;
                                $sum_total_discount = 0;
                                $sum_total_tax = 0;
                                $sum_total = 0;
                            @endphp

                            @foreach($records as $key => $value)
                            @php
                                $serie_affec = '';
                            @endphp
                            <tr>
                                <td class="celda">{{$loop->iteration}}</td>
                                <td class="celda">{{$value->user->name}}</td>
                                <td class="celda">{{$value->type_document->name}}</td>
                                <td class="celda">{{$value->series}}-{{$value->number}}</td>
                                <td class="celda">{{$value->date_of_issue->format('Y-m-d')}}</td>

                                @php
                                    if(in_array($value->type_document_id,[2,3]) && $value->reference){

                                        $series = $value->reference->series;
                                        $number =  $value->reference->number;
                                        $serie_affec = $series.' - '.$number;
                                    }

                                    
                                    $sum_sale += $value->sale;
                                    $sum_total_discount += $value->total_discount;
                                    $sum_total_tax += $value->total_tax;
                                    $sum_total += $value->total;

                                @endphp
                                <td class="celda">{{$serie_affec }} </td>

                                <td class="celda">{{ ($value->quotation) ? $value->quotation->number_full : '' }}</td>
                                <td class="celda">{{ isset($value->quotation->sale_opportunity) ? $value->quotation->sale_opportunity->number_full : '' }}</td>

                                <td class="celda">{{$value->customer->name}}</td>
                                <td class="celda">{{$value->customer->number}}</td>
                                <td class="celda">{{$value->state_document->name}}</td>

                                <td class="celda">{{$value->currency_type_id}}</td>

                                <td class="celda">{{$value->sale}}</td>
                                <td class="celda">{{$value->total_discount}}</td>
                                <td class="celda">{{$value->total_tax}}</td>
                                <td class="celda">{{$value->total}}</td>


                            </tr>
                            @endforeach

                            <tr>
                                <td class="celda" colspan="11"></td>
                                <td class="celda" >Totales:</td>
                                <td class="celda">{{ number_format($sum_sale, 2, ".", "") }}</td>
                                <td class="celda">{{ number_format($sum_total_discount, 2, ".", "") }}</td>
                                <td class="celda">{{ number_format($sum_total_tax, 2, ".", "") }}</td>
                                <td class="celda">{{ number_format($sum_total, 2, ".", "") }}</td>
                            </tr>

                        </tbody>
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
