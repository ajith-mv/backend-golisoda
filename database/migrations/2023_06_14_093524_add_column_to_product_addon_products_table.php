<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToProductAddonProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_addon_products', function (Blueprint $table) {
            $table->enum('type', ['product', 'category'])->default('product')->after('product_addon_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gbs_product_addon_products', function (Blueprint $table) {
            //
        });
    }
}
