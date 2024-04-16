<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchandiseDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchandise_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('transno');
            $table->string('client');
            $table->string('address');
            $table->string('contact');
            $table->string('consultant')->nullable();
            $table->string('associate')->nullable();
            $table->string('presenter')->nullable();
            $table->string('team_builder')->nullable();
            $table->string('distributor')->nullable();
            $table->string('code')->nullable();
            $table->string('date')->nullable();
            $table->string('price_diff')->nullable();
            $table->string('price_override')->nullable();
            $table->string('print_count')->nullable();
            $table->string('mo_no')->nullable();
            $table->string('dr_count')->nullable();
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
        Schema::dropIfExists('merchandise_deliveries');
    }
}
