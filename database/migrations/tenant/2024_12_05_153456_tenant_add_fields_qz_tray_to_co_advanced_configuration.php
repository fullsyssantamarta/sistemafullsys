<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddFieldsQzTrayToCoAdvancedConfiguration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('co_advanced_configuration', function (Blueprint $table) {
            $table->boolean('enable_qz_tray')->default(false)->after('lastsync');
            $table->string('digital_certificate_qztray')->nullable()->after('lastsync');
            $table->string('private_certificate_qztray')->nullable()->after('lastsync');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('co_advanced_configuration', function (Blueprint $table) {
            $table->dropColumn('enable_qz_tray');
            $table->dropColumn('digital_certificate_qztray');
            $table->dropColumn('private_certificate_qztray');
        });
    }
}
