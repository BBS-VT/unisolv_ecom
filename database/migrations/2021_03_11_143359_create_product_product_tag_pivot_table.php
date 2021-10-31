<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductProductTagPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_product_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id', 'product_id_fk_1191810')->references('id')->on('products')->onDelete('cascade');
            $table->unsignedBigInteger('product_tag_id');
            $table->foreign('product_tag_id', 'product_tag_id_fk_1191810')->references('id')->on('product_tags')->onDelete('cascade');
            $table->timestamps();
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
        Schema::dropIfExists('product_product_tag_pivot');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
