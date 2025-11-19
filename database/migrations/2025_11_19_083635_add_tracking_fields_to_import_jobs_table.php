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
        Schema::table('import_jobs', function (Blueprint $table) {
            $table->integer('successful_rows')->default(0)->after('processed_rows');
            $table->integer('failed_rows')->default(0)->after('successful_rows');
            $table->integer('items_updated')->default(0)->after('failed_rows');
            $table->unsignedBigInteger('company_id')->nullable()->after('items_updated');
            $table->unsignedBigInteger('imported_by')->nullable()->after('company_id');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('imported_by')->references('id')->on('users')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('import_jobs', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropForeign(['imported_by']);
            $table->dropColumn(['successful_rows', 'failed_rows', 'items_updated', 'company_id', 'imported_by']);
        });
    }
};
