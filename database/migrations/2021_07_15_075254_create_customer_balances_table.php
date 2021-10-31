<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_balances', function (Blueprint $table) {
            $table->string('AccMain', 11)->primary();
            $table->string('AccSub', 3)->default('000');
            $table->decimal('AgedBalance1', 18,3)->default('0.000');
            $table->decimal('AgedBalance2', 18,3)->default('0.000');
            $table->decimal('AgedBalance3', 18,3)->default('0.000');
            $table->decimal('AgedBalance4', 18,3)->default('0.000');
            $table->decimal('AgedBalance5', 18,3)->default('0.000');
            $table->decimal('AgedBalance6', 18,3)->default('0.000');
            $table->bigInteger('LastEditedBy')->unsigned();
            $table->timestamps();

            $table->foreign('LastEditedBy')->references('id')->on('users');
            $table->foreign('AccMain')->references('acc_main')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_balances');
    }
}
