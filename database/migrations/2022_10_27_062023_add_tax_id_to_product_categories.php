<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxIdToProductCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->string('tag_line')->nullable();
            $table->unsignedBigInteger('tax_id')->nullable()->after('tag_line');
            $table->enum('is_home_menu',['yes', 'no'])->nullable()->after('tax_id');
            $table->unsignedBigInteger('updated_by')->after('is_home_menu')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropColumn('tag_line');
            $table->dropColumn('tax_id');
            $table->dropColumn('is_home_menu');
            $table->dropForeign(['updated_by']);
            $table->dropColumn('updated_by');
            
        });
    }
}
