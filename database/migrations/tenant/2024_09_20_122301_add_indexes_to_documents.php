<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesToDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->unique(['type_document_id', 'type_environment_id', 'prefix', 'number'], 'environment_prefix_number_unique');
        });

        Schema::table('documents_pos', function (Blueprint $table) {
            $table->unique(['filename', 'prefix', 'number'], 'filename_prefix_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropUnique('environment_prefix_number_unique');
        });

        Schema::table('documents_pos', function (Blueprint $table) {
            $table->dropUnique('filename_prefix_number_unique');
        });
    }
}
