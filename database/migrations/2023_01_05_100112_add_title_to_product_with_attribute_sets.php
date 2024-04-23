<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleToProductWithAttributeSets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_with_attribute_sets', function (Blueprint $table) {
            $table->unsignedBigInteger('product_attribute_set_id')->nullable()->change();
            $table->string( 'title' )->after('product_attribute_set_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_with_attribute_sets', function (Blueprint $table) {
            $table->unsignedBigInteger('product_attribute_set_id')->nullable(false)->change();
            $table->dropColumn('title');

        });
    }
}
