<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchandiseInventoryItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchandise_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->integer('date');
            $table->string('product_id', 20);
            $table->integer('beginning_balance')->nullable()->default('0');
            $table->integer('total_delivered')->nullable()->default('0');
            $table->integer('total_released')->nullable()->default('0');
            $table->integer('total_returned')->nullable()->default('0');
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
        Schema::dropIfExists('merchandise_inventory_items');
    }
}
