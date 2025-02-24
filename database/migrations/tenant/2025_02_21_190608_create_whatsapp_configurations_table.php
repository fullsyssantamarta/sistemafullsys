<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWhatsappConfigurationsTable extends Migration
{
    public function up()
    {
        Schema::create('whatsapp_configurations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('api_url')->nullable();
            $table->string('api_token')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('whatsapp_configurations');
    }
}
