<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('custom_description')->nullable()->after('product_id');
        });

        Schema::table('delivery_items', function (Blueprint $table) {
            $table->string('custom_description')->nullable()->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('custom_description');
        });

        Schema::table('delivery_items', function (Blueprint $table) {
            $table->dropColumn('custom_description');
        });
    }
};
