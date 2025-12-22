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
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('contactable_type')->nullable()->after('company_id');
            $table->unsignedBigInteger('contactable_id')->nullable()->after('contactable_type');

            $table->string('mobile')->nullable()->after('phone');
            $table->string('position')->nullable()->after('mobile');
            $table->string('department')->nullable()->after('position');
            $table->boolean('is_active')->default(true)->after('is_primary');

            $table->index(['contactable_type', 'contactable_id']);
        });

        DB::table('contacts')
            ->whereNotNull('customer_id')
            ->update([
                'contactable_type' => 'App\\Models\\Customer',
                'contactable_id' => DB::raw('customer_id')
            ]);

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['customer_id']);
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->string('contactable_type')->nullable(false)->change();
            $table->unsignedBigInteger('contactable_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('company_id');
            $table->foreign('customer_id')->references('id')->on('customers');
        });

        DB::table('contacts')
            ->where('contactable_type', 'App\\Models\\Customer')
            ->update([
                'customer_id' => DB::raw('contactable_id')
            ]);

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex(['contactable_type', 'contactable_id']);
            $table->dropColumn([
                'contactable_type',
                'contactable_id',
                'mobile',
                'position',
                'department',
                'is_active'
            ]);
        });
    }
};
