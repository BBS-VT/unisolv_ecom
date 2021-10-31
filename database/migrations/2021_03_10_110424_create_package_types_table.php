<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_types', function (Blueprint $table) {
            $table->id();
            $table->string('PackageTypeName', 50)->unique();
            $table->bigInteger('LastEditedBy')->unsigned();
            $table->timestamps();

            $table->index(['PackageTypeName']);
            $table->foreign('LastEditedBy')->references('id')->on('users');
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
        Schema::dropIfExists('package_types');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
