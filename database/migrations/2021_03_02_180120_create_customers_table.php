<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('acc_main', 11);
            $table->string('acc_sub', 3)->default('000');
            $table->string('acc_code', 15);
            $table->string('CustomerName', 100);
            $table->bigInteger('BillToCustomerID')->unsigned()->nullable();
            $table->string('CustomerCategoryID')->nullable();
            $table->string('BuyingGroupID')->nullable();
            $table->bigInteger('PrimaryContactPersonID')->unsigned()->nullable();
            $table->bigInteger('AlternateContactPersonID')->unsigned()->nullable();
            $table->string('StoreEAN', 16)->unique();
            $table->string('VatNr', 30)->nullable();
            $table->decimal('CreditLimit', 18,2)->nullable();
            $table->date('AccountOpenedDate');
            $table->decimal('StandardDiscountPercentage', 18,3)->nullable();
            $table->tinyInteger('IsStatementSent')->unsigned()->default('0');
            $table->tinyInteger('IsOnCreditHold')->unsigned()->default('0');
            $table->bigInteger('PaymentDays')->unsigned()->nullable();
            $table->string('PhoneNumber', 20)->nullable();
            $table->string('FaxNumber', 20)->nullable();
            $table->string('DeliveryRoute', 5)->nullable();
            $table->string('DeliveryRoutePosition', 5)->nullable();
            $table->string('WebsiteURL', 256)->nullable();
            $table->string('GeneralEmailAddress', 40)->nullable();
            $table->string('DeliveryAddressLine1', 60)->nullable();
            $table->string('DeliveryAddressLine2', 60)->nullable();
            $table->string('DeliveryCity', 45)->nullable();
            $table->string('DeliveryPostalCode', 10)->nullable();
            $table->string('PostalAddressLine1', 60)->nullable();
            $table->string('PostalAddressLine2', 60)->nullable();
            $table->string('PostalCity', 45)->nullable();
            $table->string('PostalPostalCode', 10)->default('0000');
            $table->boolean('CustomerStatus')->unsigned()->default('1');
            $table->bigInteger('CountryID')->unsigned()->default('202');
            $table->bigInteger('SalesRepID')->unsigned()->nullable();
            $table->bigInteger('LastEditedBy')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['acc_main', 'acc_code', 'StoreEAN']);

            $table->foreign('CountryID')->references('id')->on('countries');
            $table->foreign('SalesRepID')->references('id')->on('users');
            $table->foreign('BillToCustomerID')->references('id')->on('customers');
            $table->foreign('BuyingGroupID')->references('BuyingGroupName')->on('buying_groups');
            $table->foreign('CustomerCategoryID')->references('AccountType')->on('customer_categories');
            $table->foreign('PrimaryContactPersonID')->references('id')->on('users');
            $table->foreign('AlternateContactPersonID')->references('id')->on('users');



        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('customers');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
