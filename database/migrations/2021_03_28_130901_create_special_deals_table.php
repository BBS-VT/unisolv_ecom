<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecialDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_deals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('StockItemID')->unsigned()->nullable();
            $table->bigInteger('CustomerID')->unsigned()->nullable();
            $table->bigInteger('BuyingGroupID')->unsigned()->nullable();
            $table->bigInteger('CustomerCategoryID')->unsigned()->nullable();
            $table->bigInteger('StockGroupID')->unsigned()->nullable();
            $table->string('DealDescription', 30);
            $table->date('StartDate');
            $table->date('EndDate');
            $table->decimal('DiscountAmount', 18,2)->nullable();
            $table->decimal('DiscountPercentage', 18,3)->nullable();
            $table->decimal('UnitPrice', 18,2)->nullable();
            $table->bigInteger('LastEditedBy')->unsigned();
            $table->timestamps();

            $table->foreign('StockItemID')->references('id')->on('products');
            $table->foreign('CustomerID')->references('id')->on('customers');
            $table->foreign('BuyingGroupID')->references('id')->on('buying_groups');
            $table->foreign('CustomerCategoryID')->references('id')->on('customer_categories');
            $table->foreign('StockGroupID')->references('id')->on('product_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('special_deals');
    }
}
