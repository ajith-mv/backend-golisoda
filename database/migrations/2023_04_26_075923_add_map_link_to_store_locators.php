<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMapLinkToStoreLocators extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_locators', function (Blueprint $table) {
            $table->text('map_link')->after('contact_no')->nullable();
            $table->text('image_360_link')->after('map_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_locators', function (Blueprint $table) {
            $table->dropColumn('map_link');
            $table->dropColumn('image_360_link');
        });
    }
}
