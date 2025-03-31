<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUuidToCustomerCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        Schema::table('customers', function (Blueprint $table) {

            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_categories', function (Blueprint $table) {

            if (Schema::hasColumn('company_id'))
            {
                Schema::table('customer_categories', function (Blueprint $table) {
                    Schema::disableForeignKeyConstraints();

                    $table->dropColumn('company_id');

                    Schema::enableForeignKeyConstraints();
                });
            }
        });
    }
}
