<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('StockItemName', 100);
            $table->string('StockCode', 20)->unique();
            $table->string('SupplierID')->nullable();
            $table->bigInteger('UnitPackageID')->unsigned()->nullable();
            $table->bigInteger('OuterPackageID')->unsigned()->nullable();
            $table->bigInteger('TaxRateID')->unsigned();
            $table->string('Brand', 50)->nullable();
            $table->string('Size', 20)->nullable();
            $table->bigInteger('LeadTimeDays')->unsigned()->nullable();
            $table->bigInteger('Packsize')->unsigned()->default('1');
            $table->string('Barcode', 50)->nullable();
            $table->string('AltBarcode', 50)->nullable();
            $table->decimal('CostPrice', 18,2);
            $table->decimal('SellingPrice', 18,2);
            $table->decimal('WeightPerUnit', 18,3)->default('0.000');
            $table->longText('MarketingComments')->nullable();
            $table->string('SearchDetails')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->bigInteger('LastEditedBy')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('LastEditedBy')->references('id')->on('users');
            $table->foreign('UnitPackageID')->references('id')->on('package_types');
            $table->foreign('OuterPackageID')->references('id')->on('package_types');
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
        Schema::dropIfExists('products');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
