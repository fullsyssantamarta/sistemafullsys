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
        Schema::dropIfExists('chart_of_accounts');
    }

    private function createAccounts()
    {
        $accounts = [
            ['code' => '1', 'name' => 'Activo', 'type' => 'Asset', 'parent_id' => null, 'level' => 1],
            ['code' => '2', 'name' => 'Pasivo', 'type' => 'Liability', 'parent_id' => null, 'level' => 1],
            ['code' => '3', 'name' => 'Patrimonio', 'type' => 'Equity', 'parent_id' => null, 'level' => 1],
            ['code' => '4', 'name' => 'Ingresos', 'type' => 'Revenue', 'parent_id' => null, 'level' => 1],
            ['code' => '5', 'name' => 'Gastos', 'type' => 'Expense', 'parent_id' => null, 'level' => 1],
            ['code' => '6', 'name' => 'Costos', 'type' => 'Cost', 'parent_id' => null, 'level' => 1],
            ['code' => '11', 'name' => 'Activo Corriente', 'type' => 'Asset', 'parent_id' => 1, 'level' => 2],
            ['code' => '21', 'name' => 'Cuentas por Pagar', 'type' => 'Liability', 'parent_id' => 2, 'level' => 2],
            ['code' => '1105', 'name' => 'Caja', 'type' => 'Asset', 'parent_id' => 7, 'level' => 3],
            ['code' => '1110', 'name' => 'Bancos', 'type' => 'Asset', 'parent_id' => 7, 'level' => 3],
            ['code' => '11050501', 'name' => 'Caja General', 'type' => 'Asset', 'parent_id' => 9, 'level' => 4],
            ['code' => '11100501', 'name' => 'Cuenta Corriente BBVA', 'type' => 'Asset', 'parent_id' => 10, 'level' => 4],
            ['code' => '11100502', 'name' => 'Cuenta Ahorros Bancolombia', 'type' => 'Asset', 'parent_id' => 10, 'level' => 4]
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::create($account);
        }
    }
}
