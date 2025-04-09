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
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedTinyInteger('price_level')->default(1)->after('LastEditedBy')->comment('Price level of the customer');
            $table->boolean('discount_allowed')->default(true)->after('price_level')->comment('Indicates if the customer is allowed to receive discounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('discount_allowed');
            $table->dropColumn('price_level');
        });
    }
};
