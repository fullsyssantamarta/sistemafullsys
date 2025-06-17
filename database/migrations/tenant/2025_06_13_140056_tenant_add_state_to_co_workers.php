<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class TenantAddStateToCoWorkers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('co_workers', function (Blueprint $table) {
            $table->boolean('state')->default(false)->after('salary');
        });
        // Actualiza los registros existentes a true
        \DB::table('co_workers')->update(['state' => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('co_workers', function (Blueprint $table) {
            $table->dropColumn('state');
        });
    }
}
