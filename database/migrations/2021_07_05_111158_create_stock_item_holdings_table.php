<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockItemHoldingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_item_holdings', function (Blueprint $table) {
            $table->string('StockCode', 12)->primary();
            $table->decimal('QuantityOnHand')->default('0');
            $table->string('BinLocation', 20)->nullable();
            $table->unsignedBigInteger('LastStocktakeQuantity')->default('0');
            $table->decimal('LastCostPrice', 18,3)->default('0.000');
            $table->unsignedBigInteger('ReorderLevel')->default('0');
            $table->unsignedBigInteger('TargetStockLevel')->default('0');
            $table->bigInteger('LastEditedBy')->unsigned();
            $table->timestamps();

            $table->foreign('LastEditedBy')->references('id')->on('users');
            $table->foreign('StockCode')->references('StockCode')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_item_holdings');
    }
}
