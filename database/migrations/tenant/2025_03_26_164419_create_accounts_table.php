<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Accounting\Models\ChartOfAccount;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 8)->unique(); // Código contable (Ej: 11050501)
            $table->string('name'); // Nombre de la cuenta (Ej: Caja General)
            $table->enum('type', ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense', 'Cost']); // Tipo de cuenta
            $table->unsignedBigInteger('parent_id')->nullable(); // Relación padre-hijo
            $table->integer('level'); // Nivel jerárquico (1 a 4)
            $table->boolean('status')->default(1); // Activo/Inactivo
            $table->timestamps();

            // Clave foránea para la relación padre-hijo
            $table->foreign('parent_id')->references('id')->on('chart_of_accounts');
        });

        $this->createAccounts();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
        });
        Schema::dropIfExists('chart_of_accounts');
    }

    private function createAccounts()
    {
        $accounts = [
            ['code' => '1', 'name' => 'ACTIVO', 'type' => 'Asset', 'level' => 1, 'parent_code' => null],
            ['code' => '11', 'name' => 'DISPONIBLE', 'type' => 'Asset', 'level' => 2, 'parent_code' => '1'],
            ['code' => '1105', 'name' => 'CAJA', 'type' => 'Asset', 'level' => 3, 'parent_code' => '11'],
            ['code' => '110505', 'name' => 'Caja General', 'type' => 'Asset', 'level' => 4, 'parent_code' => '1105'],
            ['code' => '110510', 'name' => 'Caja Menor', 'type' => 'Asset', 'level' => 4, 'parent_code' => '1105'],
            ['code' => '1110', 'name' => 'BANCOS', 'type' => 'Asset', 'level' => 3, 'parent_code' => '11'],
            ['code' => '111005', 'name' => 'Bancos Nacionales', 'type' => 'Asset', 'level' => 4, 'parent_code' => '1110'],
            ['code' => '111010', 'name' => 'Bancos del Exterior', 'type' => 'Asset', 'level' => 4, 'parent_code' => '1110'],
            ['code' => '12', 'name' => 'INVERSIONES', 'type' => 'Asset', 'level' => 2, 'parent_code' => '1'],
            ['code' => '1205', 'name' => 'Acciones', 'type' => 'Asset', 'level' => 3, 'parent_code' => '12'],
            ['code' => '1210', 'name' => 'Bonos', 'type' => 'Asset', 'level' => 3, 'parent_code' => '12'],

            ['code' => '2', 'name' => 'PASIVO', 'type' => 'Liability', 'level' => 1, 'parent_code' => null],
            ['code' => '21', 'name' => 'OBLIGACIONES FINANCIERAS', 'type' => 'Liability', 'level' => 2, 'parent_code' => '2'],
            ['code' => '2105', 'name' => 'Bancos Nacionales', 'type' => 'Liability', 'level' => 3, 'parent_code' => '21'],
            ['code' => '210505', 'name' => 'Sobregiros Bancarios', 'type' => 'Liability', 'level' => 4, 'parent_code' => '2105'],
            ['code' => '210510', 'name' => 'Préstamos Bancarios', 'type' => 'Liability', 'level' => 4, 'parent_code' => '2105'],

            ['code' => '3', 'name' => 'PATRIMONIO', 'type' => 'Equity', 'level' => 1, 'parent_code' => null],
            ['code' => '31', 'name' => 'CAPITAL SOCIAL', 'type' => 'Equity', 'level' => 2, 'parent_code' => '3'],
            ['code' => '3105', 'name' => 'Aportes de Socios', 'type' => 'Equity', 'level' => 3, 'parent_code' => '31'],

            ['code' => '4', 'name' => 'INGRESOS', 'type' => 'Revenue', 'level' => 1, 'parent_code' => null],
            ['code' => '41', 'name' => 'VENTAS', 'type' => 'Revenue', 'level' => 2, 'parent_code' => '4'],
            ['code' => '4105', 'name' => 'Venta de Mercancías', 'type' => 'Revenue', 'level' => 3, 'parent_code' => '41'],

            ['code' => '5', 'name' => 'GASTOS', 'type' => 'Expense', 'level' => 1, 'parent_code' => null],
            ['code' => '51', 'name' => 'GASTOS OPERACIONALES', 'type' => 'Expense', 'level' => 2, 'parent_code' => '5'],
            ['code' => '5105', 'name' => 'Gastos de Administración', 'type' => 'Expense', 'level' => 3, 'parent_code' => '51'],
            ['code' => '510505', 'name' => 'Sueldos y Salarios', 'type' => 'Expense', 'level' => 4, 'parent_code' => '5105'],
            ['code' => '510510', 'name' => 'Arriendos', 'type' => 'Expense', 'level' => 4, 'parent_code' => '5105'],
        ];

        foreach ($accounts as $account) {
            // Buscar el ID del padre basado en el código del padre
            $parentId = null;
            if (!is_null($account['parent_code'])) {
                $parent = ChartOfAccount::where('code', $account['parent_code'])->first();
                $parentId = $parent ? $parent->id : null;
            }

            // Crear la cuenta con el ID del padre correcto
            ChartOfAccount::create([
                'code' => $account['code'],
                'name' => $account['name'],
                'type' => $account['type'],
                'level' => $account['level'],
                'parent_id' => $parentId,
            ]);
        }
    }
}
