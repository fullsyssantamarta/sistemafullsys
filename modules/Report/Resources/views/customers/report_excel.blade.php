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
                <td colspan="9" style="height:35px; text-align: center; vertical-align: middle; font-size: 16px; font-weight: bold; padding: 10px; background-color: #0e3abd; color: white;">
                    <h1 style="font-size: 24px; margin: 0; padding-bottom: 10px;">Reporte por Cliente</h1>
                </td>
            </tr>
            <tr>
                <td colspan="9" style="height:100px; text-align: center; vertical-align: middle; font-size: 12px; font-weight: bold; padding: 10px; background-color: #0e3abd; color: white;">

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
                                <th class="">#</th>
                                <th class="">Fecha</th>
                                <th class="">Tipo Documento</th>
                                <th class="">Prefijo</th>
                                <th class="">Número</th>
                                <th class="">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $key => $value)
                            <tr>
                                <td class="celda">{{$loop->iteration}}</td>
                                <td class="celda">{{$value->date_of_issue->format('Y-m-d')}}</td> 
                                <td class="celda">{{$value->type_document->name}}</td>
                                <td class="celda">{{$value->series}}</td>
                                <td class="celda">{{$value->number}}</td>
                                <td class="celda">{{$value->total}}</td>
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
