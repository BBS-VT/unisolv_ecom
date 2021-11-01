<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountToOrdersItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders_items', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('OrderID');
            $table->string('discount_type')->after('PackageTypeID');
            $table->decimal('discount', 15, 2)->nullable()->after('Quantity');
            $table->unsignedBigInteger('discount_val')->nullable()->after('discount');
            $table->unsignedBigInteger('total')->after('TaxRate');

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
        if (Schema::hasColumn('orders_items', 'company_id'))
        {
            Schema::table('orders_items', function (Blueprint $table) {
                Schema::disableForeignKeyConstraints();

                $table->dropColumn('company_id');
                $table->dropColumn('discount_type');
                $table->dropColumn('discount');
                $table->dropColumn('discount_val');
                $table->dropColumn('total');

                Schema::enableForeignKeyConstraints();
            });
        }
    }
}
