<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountToProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('DiscountPercentage', 4, 4)->default('0.0000')->after('SellingPrice');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::disableForeignKeyConstraints();
        if (Schema::hasColumn('products', 'DiscountPercentage'))
        {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('DiscountPercentage');
            });
        }
//        Schema::enableForeignKeyConstraints();
    }
}
