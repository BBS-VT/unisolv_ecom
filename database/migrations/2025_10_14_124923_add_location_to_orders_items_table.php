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
        Schema::table('orders_items', function (Blueprint $table) {
            $table->string('LocationCode', 10)->default('0000')->after('StockItem');

            // Add index for better query performance
            $table->index('LocationCode');

        });

        // Update existing records to use default location
        DB::table('orders_items')
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
        Schema::table('orders_items', function (Blueprint $table) {
            $table->dropIndex(['LocationCode']);
            $table->dropColumn('LocationCode');
        });
    }
};
