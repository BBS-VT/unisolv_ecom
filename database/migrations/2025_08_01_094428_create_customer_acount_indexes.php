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
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['CustomerID', 'OrderDate'], 'orders_customer_date_index');
            $table->index(['CustomerID', 'OrderStatusID'], 'orders_customer_status_index');
            $table->index('OrderNumber', 'orders_number_index');
            $table->index('CustomerPurchaseOrderNumber', 'orders_po_number_index');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->index('acc_code', 'customers_account_code_index');
            $table->index('IsOnCreditHold', 'customers_credit_hold_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_customer_date_index');
            $table->dropIndex('orders_customer_status_index');
            $table->dropIndex('orders_number_index');
            $table->dropIndex('orders_po_number_index');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_acc_code_index');
            $table->dropIndex('customers_credit_hold_index');
        });
    }
};
