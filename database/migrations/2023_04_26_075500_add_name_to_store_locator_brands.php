<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameToStoreLocatorBrands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_locator_brands', function (Blueprint $table) {
            $table->unsignedBigInteger('store_locator_id')->after('id');
            $table->dropColumn('service_center_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_locator_brands', function (Blueprint $table) {
            $table->dropColumn('store_locator_id');
        });
    }
}
