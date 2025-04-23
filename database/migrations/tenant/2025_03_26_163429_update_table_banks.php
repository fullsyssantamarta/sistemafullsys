<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class UpdateTableBanks extends Migration
{
    private $banks = [
        1 => ['new' => 'Bancolombia', 'old' => 'BANCO SCOTIABANK'],
        2 => ['new' => 'Davivienda', 'old' => 'BANCO DE CREDITO DEL PERU'],
        3 => ['new' => 'Banco de Bogotá', 'old' => 'BANCO DE COMERCIO'],
        4 => ['new' => 'BBVA Colombia', 'old' => 'BANCO PICHINCHA'],
        5 => ['new' => 'Banco de Occidente', 'old' => 'BBVA CONTINENTAL'],
        6 => ['new' => 'Banco Popular', 'old' => 'INTERBANK'],
    ];

    public function up()
    {
        foreach ($this->banks as $id => $bank) {
            DB::table('banks')
                ->where('id', $id)
                ->update(['description' => $bank['new']]);
        }
    }

    public function down()
    {
        foreach ($this->banks as $id => $bank) {
            DB::table('banks')
                ->where('id', $id)
                ->update(['description' => $bank['old']]);
        }
    }
}
