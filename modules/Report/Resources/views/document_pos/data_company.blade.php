<table style="width:100%; border-collapse: collapse;">
    <tr>
        <td colspan="10" style="height:35px; text-align: center; vertical-align: middle; font-size: 16px; font-weight: bold; padding: 10px; background-color: #0e3abd; color: white;">
            <h1 style="font-size: 24px; margin: 0; padding-bottom: 10px;">Reporte por Cliente</h1>
        </td>
    </tr>
    <tr>
        <td colspan="10" style="height:100px; text-align: center; vertical-align: middle; font-size: 12px; font-weight: bold; padding: 10px; background-color: #0e3abd; color: white;">

            <span style="font-size: 12px;">Empresa: {{$company->name}}</span><br>
            <span style="font-size: 12px;">Fecha: {{date('Y-m-d')}}</span><br>
            <span style="font-size: 12px;">N° Documento: {{$company->number}}</span><br>
            <span style="font-size: 12px;">Establecimiento: {{$establishment->address}} - {{$establishment->address}} - {{$establishment->country->name}} - {{$establishment->department->name}} - {{$establishment->city->name}}</span>
        </td>
    </tr>
    <!-- Aquí seguiría el resto de tu tabla -->
</table>