<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->integer('phonecode');
            /*$table->string('shortcode');
            $table->string('title');
            $table->timestamps();*/

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
        Schema::dropIfExists('countries');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
