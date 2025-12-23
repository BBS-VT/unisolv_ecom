<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('acc_main', 11)->nullable();
            $table->string('acc_sub', 3)->default('000');
            $table->string('acc_code', 20)->unique()->index();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');

            $table->string('SupplierName');
            $table->string('VatNr', 50)->nullable();
            $table->string('tax_reference', 50)->nullable();

            // Contact Information (main numbers)
            $table->string('PhoneNumber', 20)->nullable();
            $table->string('FaxNumber', 20)->nullable();
            $table->string('WebsiteURL')->nullable();
            $table->string('GeneralEmailAddress')->nullable();

            // Banking Details
            $table->decimal('CreditLimit', 15, 2)->default(0);
            $table->integer('PaymentDays')->default(30);
            $table->string('payment_terms', 50)->nullable(); // "Net 30", "2/10 Net 30"
            $table->decimal('StandardDiscountPercentage', 5, 2)->default(0);
            $table->boolean('IsOnCreditHold')->default(false);

            // Banking Details
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_branch')->nullable();

            // Operational Details
            $table->integer('lead_time_days')->default(7); // Default delivery lead time
            $table->decimal('minimum_order_value', 15, 2)->nullable();
            $table->foreignId('currency_id')->nullable()->constrained();
            $table->foreignId('CountryID')->nullable(); // Keep for compatibility

            // Account Management
            $table->date('AccountOpenedDate')->nullable();
            $table->boolean('Status')->default(true); // 1 = active, 0 = inactive
            $table->text('notes')->nullable(); // Internal notes

            // Audit Fields
            $table->foreignId('LastEditedBy')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('company_id');
            $table->index('Status');
            $table->index(['company_id', 'Status']);
        });

        // Product-Supplier pivot table
        Schema::create('product_supplier', function (Blueprint $table) {
            $table->id();
            $table->string('StockCode', 50); // Your products use StockCode
            $table->unsignedBigInteger('supplier_id');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');

            // Supplier-specific product details
            $table->string('supplier_product_code', 100)->nullable(); // Their SKU/code
            $table->decimal('cost_price', 15, 2)->nullable(); // Their price
            $table->integer('lead_time_days')->nullable(); // Override default
            $table->boolean('is_preferred')->default(false); // Preferred supplier for this product
            $table->integer('sort_order')->default(0); // For ordering multiple suppliers

            $table->timestamps();

            // Foreign keys
            $table->foreign('StockCode')->references('StockCode')->on('products')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');

            // Indexes
            $table->unique(['StockCode', 'supplier_id', 'company_id']);
            $table->index('company_id');
            $table->index('is_preferred');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_supplier');
        Schema::dropIfExists('suppliers');
    }
};
