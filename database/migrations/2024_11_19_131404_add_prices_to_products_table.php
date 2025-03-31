<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPricesToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('CostPrice', 'AverageCostPrice');
            $table->decimal('SellingPrice2', 10, 2)->nullable()->after('SellingPrice');
            $table->decimal('SellingPrice3', 10, 2)->nullable()->after('SellingPrice2');
            $table->decimal('SellingPrice4', 10, 2)->nullable()->after('SellingPrice3');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('AverageCostPrice', 'CostPrice');
            $table->dropColumn('SellingPrice2');
            $table->dropColumn('SellingPrice3');
        });
    }
}
