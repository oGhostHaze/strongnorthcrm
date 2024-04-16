<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplyInventoryItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supply_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->integer('date');
            $table->string('item_id', 20);
            $table->integer('beginning_balance')->nullable()->default('0');
            $table->integer('added')->nullable()->default('0');
            $table->integer('disposed')->nullable()->default('0');
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
        Schema::dropIfExists('supply_inventory_items');
    }
}
