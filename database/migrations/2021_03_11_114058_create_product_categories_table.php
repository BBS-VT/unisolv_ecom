<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('ParentID')->default(0);
            $table->string('StockGroupName')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->bigInteger('LastEditedBy')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['StockGroupName']);
            $table->foreign('LastEditedBy')->references('id')->on('users');
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
        Schema::dropIfExists('product_categories');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
