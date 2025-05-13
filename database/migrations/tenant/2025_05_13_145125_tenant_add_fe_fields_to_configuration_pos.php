<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddFEFieldsToConfigurationPos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configuration_pos', function (Blueprint $table) {
            $table->string('type_resolution')->nullable()->default('Documento Equivalente POS Electronico')->after('electronic');
            $table->string('technical_key')->nullable()->after('cash_type');
        });
        \DB::table('configuration_pos')->whereNull('type_resolution')->update(['type_resolution' => 'Documento Equivalente POS Electronico']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configuration_pos', function (Blueprint $table) {
            $table->dropColumn('type_resolution');
            $table->dropColumn('technical_key');
        });
    }
}
