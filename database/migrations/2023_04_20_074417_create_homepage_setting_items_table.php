<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomepageSettingItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('homepage_setting_items', function (Blueprint $table) {
            $table->id();         
            $table->unsignedBigInteger('homepage_settings_id');
            $table->float('start_size',8,2);
            $table->float('end_size',8,2);
            $table->string('setting_image_name');
            $table->enum( 'status', ['published', 'unpublished'])->default('published');
            $table->integer('order_by')->nullable();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('homepage_setting_items');
    }
}
