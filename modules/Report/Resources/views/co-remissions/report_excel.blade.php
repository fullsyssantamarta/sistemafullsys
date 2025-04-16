<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Remisiones</title>
    </head>
    <body>
        <div>
            <h3 align="center" class="title"><strong>Reporte Remisiones</strong></h3>
        </div>
        <br>
        <div style="margin-top:20px; margin-bottom:15px;">
            <table>
                <tr>
                    <td>
                        <p><b>Empresa: </b></p>
                    </td>
                    <td align="center">
                        <p><strong>{{$company->name}}</strong></p>
                    </td>
                    <td>
                        <p><strong>Fecha: </strong></p>
                    </td>
                    <td align="center">
                        <p><strong>{{date('Y-m-d')}}</strong></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p><strong>N° Documento: </strong></p>
                    </td>
                    <td align="center">{{$company->number}}</td>
                  
                </tr>
            </table>
        </div>
        <br>
        @if(!empty($records))
            <div class="">
                <div class=" ">
                <table class="">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha Emisión</th>
                                <th class="">Usuario/Vendedor</th>
                                <th>Cliente</th>
                                <th>Codigo Producto</th>
                                <th>Estado</th>
                                <th>Remisión</th>
                                <th>Cotización</th>
                                <th class="text-center">Moneda</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total_general = 0;
                            @endphp
                            @foreach($records as $key => $value)
                                @php
                                    $row = $value->getRowResource();
                                    $total_general += $row['total'];
                                @endphp
                                <tr>
                                    <td class="celda">{{$loop->iteration}}</td>
                                    <td class="celda">{{$row['date_of_issue']}}</td>
                                    <td class="celda">{{$row['user_name']}}</td>
                                    <td class="celda">{{$row['customer_name']}}</td>
                                    <td class="celda">
                                        @foreach($value->items as $item)
                                            {{$item->item->internal_id ?? 'N/A'}}
                                            @if(!$loop->last), @endif
                                        @endforeach
                                    </td>
                                    <td class="celda">{{$row['state_type_description']}}</td>
                                    <td class="celda">{{$row['number_full']}}</td>
                                    <td class="celda">{{$row['quotation_number_full']}}</td>
                                    <td class="celda">{{$row['currency_name']}}</td>
                                    <td class="celda">{{$row['total']}}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="8" class="celda"></td>
                                <td class="celda"><strong>TOTAL GENERAL</strong></td>
                                <td class="celda"><strong>{{number_format($total_general, 2)}}</strong></td>
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
