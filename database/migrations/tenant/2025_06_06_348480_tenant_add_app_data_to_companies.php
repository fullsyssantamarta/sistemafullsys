<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TenantAddAppDataToCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('companies', function(Blueprint $table) {
            $table->string('app_name')->nullable()->after('logo_login')->default(env('APP_NAME', ''));
            $table->string('app_owner_name')->nullable()->after('app_name')->default(config('tenant.app_owner_name', ''));
            $table->string('app_business_name')->nullable()->after('app_owner_name')->default(config('tenant.app_business_name', ''));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('companies', function(Blueprint $table) {
            $table->dropColumn('app_name');
            $table->dropColumn('app_owner_name');
            $table->dropColumn('app_business_name');
        });
    }
}
