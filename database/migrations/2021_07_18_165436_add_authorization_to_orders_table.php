<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAuthorizationToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedTinyInteger('Authorisation')->default('0')->after('OrderStatusID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('orders', 'Authorisation'))
        {
            Schema::table('orders', function (Blueprint $table) {
                Schema::disableForeignKeyConstraints();

                $table->dropColumn('Authorisation');

                Schema::enableForeignKeyConstraints();
            });
        }
    }
}
