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
                <td colspan="10" style="height:35px; text-align: center; vertical-align: middle; font-size: 16px; font-weight: bold; padding: 10px; background-color: #0e3abd; color: white;">
                    <h1 style="font-size: 24px; margin: 0; padding-bottom: 10px;">Reporte de comisión de vendedores</h1>
                </td>
            </tr>
            <tr>
                <td colspan="10" style="height:100px; text-align: center; vertical-align: middle; font-size: 12px; font-weight: bold; padding: 10px; background-color: #0e3abd; color: white;">
        
                    <span style="font-size: 12px;">Empresa: {{$company->name}}</span><br>
                    <span style="font-size: 12px;">Fecha: {{date('Y-m-d')}}</span><br>
                    <span style="font-size: 12px;">N° Documento: {{$company->number}}</span><br>                    
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
                                <th class="text-center">Cantidad transacciones</th>
                                <th class="text-center">Ventas acumuladas</th>
                                <th class="text-center">Total comisiones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $row)
                                @php
                                
                                    $total_commision = 0;
                                    $total_commision_document = 0;
                                    $total_commision_sale_note = 0;

                                    $total_transactions_document = $row->documents->count();
                                    $total_transactions_sale_note = $row->sale_notes->count();
                                    $total_transactions = $total_transactions_document + $total_transactions_sale_note;

                                    $acum_sales_document = $row->documents->sum('total');
                                    $acum_sales_sale_note = $row->sale_notes->sum('total');
                                    $acum_sales = $acum_sales_document + $acum_sales_sale_note;


                                    foreach ($row->documents as $document) {
                                        // $total_commision_document += $document->items->sum('relation_item.commission_amount'); 
                                        foreach ($document->items as $item) {
                                            if ($item->relation_item->commission_amount) {

                                                if(!$item->relation_item->commission_type || $item->relation_item->commission_type == 'amount'){

                                                    $total_commision_document += $item->quantity * $item->relation_item->commission_amount;
                                                }
                                                else{

                                                    $total_commision_document += $item->quantity * $item->unit_price * ($item->relation_item->commission_amount/100);
                                                    
                                                }

                                                //$total_commision_document += $item->quantity * $item->relation_item->commission_amount;
                                            }
                                        } 

                                    }

                                    foreach ($row->sale_notes as $sale_note) {
                                        // $total_commision_sale_note += $sale_note->items->sum('relation_item.commission_amount'); 
                                        foreach ($sale_note->items as $item) {
                                            if ($item->relation_item->commission_amount) {
                                                
                                                if(!$item->relation_item->commission_type || $item->relation_item->commission_type == 'amount'){

                                                    $total_commision_sale_note += $item->quantity * $item->relation_item->commission_amount;
                                                }
                                                else{

                                                    $total_commision_sale_note += $item->quantity * $item->unit_price * ($item->relation_item->commission_amount/100);
                                                    
                                                }
                                                
                                                //$total_commision_sale_note += ($item->quantity * $item->relation_item->commission_amount);
                                            }
                                        }
                                    }

                                    $total_commision = $total_commision_document + $total_commision_sale_note;
                                @endphp
                                
                                <tr>
                                    <td class="celda" >{{$loop->iteration}}</td>
                                    <td class="celda">{{$row->name}}</td>
                                    <td class="celda">{{$total_transactions}}</td>
                                    <td class="celda">{{$acum_sales}}</td> 
                                    <td class="celda">{{$total_commision}}</td> 
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
