<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlobalSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name');
            $table->string('site_email')->nullable();
            $table->string('site_mobile_no')->nullable();
            $table->string('favicon')->nullable();
            $table->string('logo')->nullable();
            $table->text('copyrights')->nullable();
            $table->integer('enable_mail')->nullable();
            $table->integer('enable_sms')->nullable();
            $table->string('payment_mode')->nullable()->default('test')->comment('test,live');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('global_settings');
    }
}
