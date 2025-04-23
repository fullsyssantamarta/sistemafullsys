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
        @if(!empty($records))
            <div class="">
                <div class=" "> 
                    <table class="">
                        <thead>
                            <tr>
                                <th>Código tipo de persona</th>
                                <th>Código tipo de régimen</th>
                                <th>Código tipo de obligación</th>
                                <th>Código tipo de documento</th>
                                <th>Número de identificación</th>
                                <th>DV</th>
                                <th>Código Interno</th>
                                <th>Nombre completo</th>
                                <th>Código de ciudad</th>
                                <th>Dirección</th>
                                <th>Teléfono</th>
                                <th>Correo electrónico</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $value)
                            <tr>
                                <td>{{$value->type_person_id}}</td>
                                <td>{{$value->type_regime_id}}</td>
                                <td>{{$value->type_obligation_id}}</td>
                                <td>{{$value->identity_document_type_id}}</td>
                                <td>{{$value->number}}</td>
                                <td>{{$value->dv}}</td>
                                <td>{{$value->code}}</td>
                                <td>{{$value->name}}</td>
                                <!-- <td>{{$value->country_id}}</td>
                                <td>{{$value->department_id}}</td> -->
                                <td>{{$value->city_id}}</td>
                                <td>{{$value->address}}</td>
                                <td>{{$value->telephone}}</td>
                                <td>{{$value->email}}</td>
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