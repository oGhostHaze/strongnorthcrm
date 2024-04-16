<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchandiseDeliveryItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchandise_delivery_items', function (Blueprint $table) {
            $table->id();
            $table->string('transno');
            $table->integer('product_id');
            $table->decimal('item_price', 18, 2);
            $table->integer('item_qty');
            $table->decimal('item_total', 18, 2);
            $table->string('status');
            $table->string('type');
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
        Schema::dropIfExists('merchandise_delivery_items');
    }
}
