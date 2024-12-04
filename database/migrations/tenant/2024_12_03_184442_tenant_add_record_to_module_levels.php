<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddRecordToModuleLevels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('module_levels')->insert([
            ['id' => 10, 'value' => 'remissions', 'description' => 'Remisiones', 'module_id' => 1],
        ]);

        $userAdminExists = DB::table('users')
            ->where('id', 1)
            ->exists();

        if ($userAdminExists) {
            DB::table('module_level_user')->insert([
                ['module_level_id' => 10, 'user_id' => 1],
            ]);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
