<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUuidToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('uid')->unique()->nullable()->after('id');
            $table->unsignedBigInteger('company_id')->nullable()->after('uid');
            $table->string('tax_per_item')->after('CustomerPurchaseOrderNumber');
            $table->string('discount_per_item')->after('tax_per_item');
            $table->string('discount_type')->nullable()->after('InternalComments');
            $table->unsignedBigInteger('discount_val')->nullable()->after('PickingCompletedWhen');
            $table->unsignedBigInteger('sub_total')->after('discount_val');
            $table->unsignedBigInteger('total')->after('sub_total');

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
        if (Schema::hasColumn('orders', 'uid'))
        {
            Schema::table('orders', function (Blueprint $table) {
                Schema::disableForeignKeyConstraints();

                $table->dropColumn('uid');
                $table->dropColumn('company_id');
                $table->dropColumn('tax_per_item');
                $table->dropColumn('discount_per_item');
                $table->dropColumn('discount_type');
                $table->dropColumn('discount_val');
                $table->dropColumn('sub_total');
                $table->dropColumn('total');

                Schema::enableForeignKeyConstraints();
            });
        }
    }
}
