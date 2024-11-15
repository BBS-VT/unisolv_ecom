<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->string('CategoryCode', 4)->unique()->after('ParentID'); // Add the category code
            $table->integer('ParentID')->nullable()->change(); // Allow null for top-level categories
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_categories', function (Blueprint $table) {

            if (Schema::hasColumn('CategoryCode'))
            {
                Schema::table('product_categories', function (Blueprint $table) {
                    Schema::disableForeignKeyConstraints();

                    $table->dropColumn('CategoryCode');

                    Schema::enableForeignKeyConstraints();
                });
            }
        });
    }
}
