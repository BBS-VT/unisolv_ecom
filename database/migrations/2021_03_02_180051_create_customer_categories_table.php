<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_categories', function (Blueprint $table) {
            $table->id();
            $table->string('CustomerCategoryName', 50)->unique();
            $table->dateTime('ValidFrom')->nullable();
            $table->dateTime('ValidTo')->nullable();
            $table->bigInteger('LastEditedBy')->unsigned();
            $table->timestamps();

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
        Schema::dropIfExists('customer_categories');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
