<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchandiseOrderReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchandise_order_returns', function (Blueprint $table) {
            $table->id();
            $table->string('product_id');
            $table->string('qty');
            $table->timestamp('date_returned');
            $table->string('received_by');
            $table->string('oa_id');
            $table->string('item_type');
            $table->integer('return_no');
            $table->string('reason')->nullable();
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
        Schema::dropIfExists('merchandise_order_returns');
    }
}
