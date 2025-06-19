<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddNCNDResolutionsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('nc_resolution_id')->nullable()->default(null)->after('fe_resolution_id');
            $table->foreign('nc_resolution_id')->references('id')->on('co_type_documents')->onDelete('set null')->onUpdate('cascade');
            $table->unsignedInteger('nd_resolution_id')->nullable()->default(null)->after('nc_resolution_id');
            $table->foreign('nd_resolution_id')->references('id')->on('co_type_documents')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nc_resolution_id');
            $table->dropColumn('nd_resolution_id');
        });
    }
}
