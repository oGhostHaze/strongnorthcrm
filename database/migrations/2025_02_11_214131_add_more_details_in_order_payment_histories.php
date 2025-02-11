<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreDetailsInOrderPaymentHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_payment_histories', function (Blueprint $table) {
            $table->date('due_date')->nullable();
            $table->date('pdc_date')->nullable();
            $table->string('reference_no')->nullable();
            $table->date('recon_date')->nullable();
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
            $table->dropColumn('due_date', 'pdc_date', 'reference_no', 'recon_date');
        });
    }
}