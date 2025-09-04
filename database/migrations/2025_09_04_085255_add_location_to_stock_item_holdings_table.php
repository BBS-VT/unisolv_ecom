<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_item_holdings', function (Blueprint $table) {
            $table->string('LocationCode', 10)->default('0000')->after('StockCode');

            $table->index(['StockCode', 'LocationCode'], 'idx_stock_location');
            $table->index('LocationCode', 'idx_location_code');

            //$table->dropPrimary('StockCode');

            //$table->id()->first();
        });

        Schema::table('stock_item_holdings', function (Blueprint $table) {
            $table->unique(['StockCode', 'LocationCode'], 'unique_stock_location');
        });

        DB::table('stock_item_holdings')
            ->whereNull('LocationCode')
            ->orWhere('LocationCode', '')
            ->update(['LocationCode' => '0000']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_item_holdings', function (Blueprint $table) {
            $table->dropUnique('unique_stock_location');

            $table->dropIndex('idx_stock_location');
            $table->dropIndex('idx_location_code');

            $table->dropColumn('LocationCode');
            $table->dropColumn('id');
        });
    }
};
