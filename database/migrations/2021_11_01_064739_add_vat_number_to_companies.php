<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVatNumberToCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('vat_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('companies', 'vat_number'))
        {
            Schema::table('companies', function (Blueprint $table) {
                Schema::disableForeignKeyConstraints();

                $table->dropColumn('vat_number');

                Schema::enableForeignKeyConstraints();
            });
        }
    }
}
