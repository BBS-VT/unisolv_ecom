<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuyingGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buying_groups', function (Blueprint $table) {
            $table->id();
            $table->string('BuyingGroupName', 50)->unique();
            $table->dateTime('ValidFrom');
            $table->dateTime('ValidTo');
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
        Schema::dropIfExists('buying_groups');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
