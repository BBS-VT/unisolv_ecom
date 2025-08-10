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
        Schema::create('promotion_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->string('stock_code', 50);
            $table->integer('quantity_purchased');
            $table->integer('quantity_discounted');
            $table->integer('bonus_quantity')->default(0);
            $table->integer('original_price_cents');
            $table->integer('discounted_price_cents');
            $table->integer('total_savings_cents');
            $table->integer('customer_price_level');
            $table->json('promotion_details')->nullable();
            $table->timestamps();

            $table->index(['promotion_id', 'customer_id']);
            $table->index('stock_code');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotion_usages');
    }
};
