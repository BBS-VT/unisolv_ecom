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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->enum('type', [
                'date_range',
                'bogo',
                'quantity_break',
                'bonus_quantity',
                'price_break',
                'online_only'
            ]);
            $table->enum('status', ['active', 'inactive', 'scheduled', 'expired'])->default('active');
            $table->datetime('starts_at');
            $table->datetime('ends_at');
            $table->string('location_code', 50)->nullable();
            $table->string('location_name', 255)->nullable();
            $table->boolean('is_online_only')->default(false);
            $table->boolean('is_imported')->default(false);
            $table->string('stock_code', 50);
            $table->json('customer_tiers')->nullable();
            $table->integer('sale_price_1')->nullable();
            $table->integer('sale_price_2')->nullable();
            $table->integer('sale_price_3')->nullable();
            $table->integer('sale_price_4')->nullable();
            $table->enum('quantity_type', ['fixed', 'break'])->default('fixed');
            $table->integer('min_quantity')->default(1);
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->integer('discount_amount')->nullable();
            $table->json('price_breaks')->nullable();
            $table->integer('buy_quantity')->nullable();
            $table->integer('get_quantity')->nullable();
            $table->json('bonus_breaks')->nullable();
            $table->integer('quantity_limit_per_customer')->nullable();
            $table->integer('usage_limit_total')->nullable();
            $table->integer('usage_count')->default(0);
            $table->string('import_batch_id', 100)->nullable();
            $table->timestamp('last_imported_at')->nullable();
            $table->timestamps();

            $table->index('stock_code');
            $table->index(['starts_at', 'ends_at']);
            $table->index(['status', 'is_online_only']);
            $table->index('location_code');
            $table->index('import_batch_id');

            $table->foreign('stock_code')->references('StockCode')->on('products')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotions');
    }
};
