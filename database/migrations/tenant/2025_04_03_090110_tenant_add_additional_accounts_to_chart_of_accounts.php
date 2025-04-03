<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Accounting\Models\ChartOfAccount;

class TenantAddAdditionalAccountsToChartOfAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $accounts = [
            ['code' => '143505', 'name' => 'Inventario de Mercancías', 'type' => 'Asset', 'level' => 4, 'parent_code' => '1435'],
            ['code' => '135515', 'name' => 'IVA descontable', 'type' => 'Asset', 'level' => 4, 'parent_code' => '1355'],
            ['code' => '220505', 'name' => 'Cuentas por pagar a proveedores', 'type' => 'Liability', 'level' => 4, 'parent_code' => '2205'],
            ['code' => '130505', 'name' => 'Cuentas por cobrar', 'type' => 'Asset', 'level' => 4, 'parent_code' => '1305'],
            ['code' => '413505', 'name' => 'Ingresos por ventas', 'type' => 'Revenue', 'level' => 4, 'parent_code' => '4135'],
            ['code' => '240805', 'name' => 'IVA generado', 'type' => 'Liability', 'level' => 4, 'parent_code' => '2408'],
        ];

        foreach ($accounts as $account) {
            // Buscar el ID del padre basado en el código de la cuenta padre
            $parent = ChartOfAccount::where('code', $account['parent_code'])->first();
            $parentId = $parent ? $parent->id : null;

            // Insertar solo si no existe ya en la base de datos
            ChartOfAccount::firstOrCreate([
                'code' => $account['code']
            ], [
                'name' => $account['name'],
                'type' => $account['type'],
                'level' => $account['level'],
                'parent_id' => $parentId,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $codes = ['143505', '135515', '220505', '130505', '413505', '240805'];
        ChartOfAccount::whereIn('code', $codes)->delete();
    }
}
