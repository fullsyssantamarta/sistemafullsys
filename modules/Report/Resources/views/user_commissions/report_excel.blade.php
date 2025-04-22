<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Comisión vendores</title>
    </head>
    <body>
        <table style="width:100%; border-collapse: collapse;">
            <tr>
                <td colspan="6" style="height:35px; text-align: center; vertical-align: middle; font-size: 12px; font-weight: bold; padding: 10px; background-color: #0e3abd; color: white;">
                    <h1 style="font-size: 24px; margin: 0; padding-bottom: 10px;">Reporte de comisión de vendedores - utilidades</h1>
                </td>
            </tr>
            <tr>
                <td colspan="6" style="height:100px; text-align: center; vertical-align: middle; font-size: 12px; font-weight: bold; padding: 10px; background-color: #0e3abd; color: white;">
        
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
                                <th>Vendedor</th>
                                <th class="text-center">Tipo comisión</th>
                                <th class="text-center">Monto comisión</th>
                                <th class="text-center">Total utilidad</th>
                                <th class="text-center">Total comisiones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $row)
                                @php

                                    $utilities = Modules\Report\Helpers\UserCommissionHelper::getUtilities($row->sale_notes, $row->documents);
                                    $commission = Modules\Report\Helpers\UserCommissionHelper::getCommission($row, $utilities);

                                @endphp
                                
                                <tr>
                                    <td class="celda" >{{$loop->iteration}}</td>
                                    <td class="celda">{{$row->name}}</td>
                                    <td class="celda">{{($row->user_commission->type == 'amount') ? 'Monto':'Porcentaje'}}</td>
                                    <td class="celda">{{$row->user_commission->amount}}</td> 
                                    <td class="celda">{{$utilities['total_utility']}}</td> 
                                    <td class="celda">{{$commission}}</td> 
                                </tr>
                            @endforeach
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
