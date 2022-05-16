<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUuidToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->uuid('uid')->unique()->nullable()->after('id');
            $table->unsignedBigInteger('company_id')->nullable()->after('uid');
            $table->unsignedBigInteger('currency_id')->nullable()->after('company_id');

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
        if (Schema::hasColumn('customers', 'uid'))
        {
            Schema::table('customers', function (Blueprint $table) {
                Schema::disableForeignKeyConstraints();

                $table->dropColumn('uid');
                $table->dropColumn('company_id');

                Schema::enableForeignKeyConstraints();
            });
        }
    }
}
