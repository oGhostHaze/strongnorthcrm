<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInfoIdToOrderReturnInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_return_infos', function (Blueprint $table) {
            $table->string('dr_no')->nullable()->after('oa_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_return_infos', function (Blueprint $table) {
            $table->dropColumn('dr_no');
        });
    }
}
