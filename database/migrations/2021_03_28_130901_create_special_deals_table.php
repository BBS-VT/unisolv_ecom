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
            $table->string('StockItemID', 50)->nullable();
            $table->string('CustomerID', 11)->nullable();
            $table->string('BuyingGroupID')->nullable();
            $table->string('CustomerCategoryID');
            $table->bigInteger('StockGroupID')->unsigned()->nullable();
            $table->string('DealDescription', 30);
            $table->date('StartDate');
            $table->date('EndDate');
            $table->decimal('DiscountAmount', 18,2)->nullable();
            $table->decimal('DiscountPercentage', 18,3)->nullable();
            $table->decimal('UnitPrice', 18,2)->nullable();
            $table->bigInteger('LastEditedBy')->unsigned();
            $table->timestamps();

            $table->foreign('StockItemID')->references('StockCode')->on('products');
            $table->foreign('CustomerID')->references('acc_main')->on('customers');
            $table->foreign('BuyingGroupID')->references('BuyingGroupName')->on('buying_groups');
            $table->foreign('CustomerCategoryID')->references('AccountType')->on('customer_categories');
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
