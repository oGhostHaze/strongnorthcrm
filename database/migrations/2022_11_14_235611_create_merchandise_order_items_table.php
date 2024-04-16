<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchandiseOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchandise_order_items', function (Blueprint $table) {
            $table->id();
            $table->string('transno');
            $table->integer('product_id');
            $table->decimal('item_price', 18, 2)->default(0.00);
            $table->integer('item_qty_ordered')->default(0);
            $table->integer('item_qty_released')->default(0);
            $table->integer('item_qty_returned')->default(0);
            $table->decimal('item_total', 18, 2)->default(0.00);
            $table->string('type')->nullable();
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
        Schema::dropIfExists('merchandise_order_items');
    }
}
