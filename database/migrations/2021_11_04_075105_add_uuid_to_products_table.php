<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUuidToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->uuid('uid')->unique()->nullable()->after('id');
            $table->unsignedBigInteger('company_id')->nullable()->after('uid');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('products', 'uid'))
        {
            Schema::table('products', function (Blueprint $table) {
                Schema::disableForeignKeyConstraints();

                $table->dropColumn('uid');
                $table->dropColumn('company_id');

                Schema::enableForeignKeyConstraints();
            });
        }
    }
}
