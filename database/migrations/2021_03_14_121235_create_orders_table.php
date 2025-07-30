<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('CustomerID');
            $table->bigInteger('SalesPersonID')->unsigned();
            $table->bigInteger('PickedByPersonID')->unsigned()->nullable();
            $table->bigInteger('ContactPersonID')->unsigned()->nullable();
            $table->bigInteger('BackorderOrderID')->unsigned()->nullable();
            $table->string('OrderNumber');
            $table->date('OrderDate');
            $table->date('ExpectedDeliveryDate')->nullable();
            $table->string('CustomerPurchaseOrderNumber', 20)->nullable();
            $table->longText('Comments')->nullable();
            $table->longText('DeliveryInstructions')->nullable();
            $table->longText('InternalComments')->nullable();
            $table->dateTime('PickingCompletedWhen')->nullable();
            $table->bigInteger('LastEditedBy')->unsigned();
            $table->timestamps();

            $table->foreign('CustomerID')->references('acc_code')->on('customers');
            $table->foreign('SalesPersonID')->references('id')->on('users');
            $table->foreign('PickedByPersonID')->references('id')->on('users');
            $table->foreign('ContactPersonID')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('orders');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

