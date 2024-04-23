<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCityColumnToCustomerAddresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->dropColumn('city_id');
            $table->dropColumn('state_id');
            $table->dropColumn('country_id');
            $table->dropColumn('post_code_id');
            $table->unsignedBigInteger('cityid')->nullable()->after('landmark');
            $table->string('city')->nullable()->after('cityid');
            $table->unsignedBigInteger('stateid')->nullable()->after('landmark');
            $table->string('state')->nullable()->after('stateid');
            $table->unsignedBigInteger('countryid')->nullable()->after('landmark');
            $table->string('country')->nullable()->after('countryid');
            $table->string('post_code')->nullable()->after('country');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_addresses', function (Blueprint $table) {
            //
        });
    }
}
