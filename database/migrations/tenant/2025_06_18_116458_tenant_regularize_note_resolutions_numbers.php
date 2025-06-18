<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Modules\Factcolombia1\Http\Controllers\Tenant\ConfigurationController;

class TenantRegularizeNoteResolutionsNumbers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $notes = new ConfigurationController();
        \DB::table('co_type_documents')->whereIn('code', [4, 5])->update(['resolution_number' => "12345"]);
        $notes->storeResolutionNote();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
