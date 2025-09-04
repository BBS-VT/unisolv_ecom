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
        Schema::create('locations', function (Blueprint $table) {
            $table->string('LocationCode', 10)->primary();
            $table->string('LocationName', 100);
            $table->text('LocationDescription')->nullable();

            $table->string('Address1', 255)->nullable();
            $table->string('Address2', 255)->nullable();
            $table->string('City', 100)->nullable();
            $table->string('Province', 100)->nullable();
            $table->string('PostalCode', 20)->nullable();
            $table->string('Country', 100)->nullable();

            $table->string('Phone', 50)->nullable();
            $table->string('Email', 255)->nullable();
            $table->string('ContactPerson', 255)->nullable();

            $table->boolean('IsActive')->default(true);
            $table->boolean('IsDefault')->default(false);
            $table->integer('SortOrder')->default(0);

            $table->string('LastEditedBy', 100)->nullable();
            $table->timestamps();

            $table->index('IsActive');
            $table->index('IsDefault');
            $table->index('SortOrder');
        });

        DB::table('locations')->insert([
            'LocationCode' => '0000',
            'LocationName' => 'Default',
            'LocationDescription' => 'This is the default location.',
            'IsActive' => true,
            'IsDefault' => true,
            'SortOrder' => 1,
            'LastEditedBy' => 'system',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
};
