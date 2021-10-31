<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->bigInteger('OrderStatusID')->after('BackorderOrderID')->unsigned();
            Schema::disableForeignKeyConstraints();
            $table->foreign('OrderStatusID')->references('id')->on('order_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            Schema::disableForeignKeyConstraints();
            $table->dropForeign(['StatusID']);
            $table->dropColumn('StatusID');
            Schema::enableForeignKeyConstraints();
        });
    }
}
