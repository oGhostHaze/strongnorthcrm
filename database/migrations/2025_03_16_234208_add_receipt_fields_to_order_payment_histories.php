<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceiptFieldsToOrderPaymentHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_payment_histories', function (Blueprint $table) {
            $table->string('batch_receipt_number')->nullable()->after('id');
            $table->integer('receipt_sequence')->nullable()->after('receipt_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_payment_histories', function (Blueprint $table) {
            $table->dropColumn('batch_receipt_number');
            $table->dropColumn('receipt_sequence');
        });
    }
}