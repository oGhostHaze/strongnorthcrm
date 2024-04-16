<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplyItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supply_items', function (Blueprint $table) {
            $table->id();
            $table->date('date_purchased');
            $table->string('item_name');
            $table->integer('qty');
            $table->string('unit');
            $table->string('location');
            $table->string('category');
            $table->decimal('unit_price', 18, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supply_items');
    }
}
