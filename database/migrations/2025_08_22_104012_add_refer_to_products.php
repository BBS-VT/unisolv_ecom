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
        Schema::table('products', function (Blueprint $table) {
            $table->string('refer_code')->nullable()->after('Packsize');

            $table->index(['refer_code']);
            $table->index(['StockCode', 'refer_code']);
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
            $table->dropIndex(['refer_code']);
            $table->dropIndex(['stock_code', 'refer_code']);
            $table->dropColumn(['refer_code']);
        });
    }
};
