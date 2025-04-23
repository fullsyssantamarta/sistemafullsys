<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsCudeToRececeivedDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('co_received_documents', function (Blueprint $table) {
            $table->string('cude_acu_recibo', 100)->nullable()->after('response_api');
            $table->string('cude_rec_bienes', 100)->nullable()->after('cude_acu_recibo');
            $table->string('cude_aceptacion', 100)->nullable()->after('cude_rec_bienes');
            $table->string('cude_rechazo', 100)->nullable()->after('cude_aceptacion');
        });
    }

    public function down()
    {
        Schema::table('co_received_documents', function (Blueprint $table) {
            $table->dropColumn([
                'cude_acu_recibo',
                'cude_rec_bienes',
                'cude_aceptacion',
                'cude_rechazo'
            ]);
        });
    }
}
