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
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('StockCode', 50)->index();
            $table->string('LocationCode', 20)->index();
            $table->enum('transaction_type', [
                'order',
                'return',
                'adjustment',
                'transfer',
                'initial',
                'import'
            ])->index();
            $table->decimal('quantity_change', 10, 2); // Positive or negative
            $table->decimal('quantity_before', 10, 2);
            $table->decimal('quantity_after', 10, 2);
            $table->string('reference_type', 50)->nullable(); // 'Order', 'Return', etc.
            $table->unsignedBigInteger('reference_id')->nullable(); // Order ID, etc.
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('company_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            // Index for lookups
            $table->index(['reference_type', 'reference_id']);
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
        Schema::dropIfExists('stock_transactions');
    }
};
