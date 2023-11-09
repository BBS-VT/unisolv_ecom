<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('OrderID');
            $table->string('StockItem', 20);
            $table->unsignedBigInteger('PackageTypeID')->nullable();
            $table->decimal('Quantity', 15,2);
            $table->decimal('UnitPrice', 18,3);
            $table->decimal('TaxRate', 18,3);
            $table->unsignedBigInteger('PickedQuantity')->nullable();
            $table->dateTime('PickingCompletedWhen')->nullable();
            $table->bigInteger('LastEditedBy')->unsigned();
            $table->timestamps();

            $table->foreign('OrderID')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('PackageTypeID')->references('id')->on('package_types');
            $table->foreign('LastEditedBy')->references('id')->on('users');
            $table->foreign('StockItem')->references('id')->on('products');
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
        Schema::dropIfExists('orders_items');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
