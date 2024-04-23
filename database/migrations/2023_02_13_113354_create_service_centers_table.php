<?php

use Doctrine\DBAL\Schema\Table;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceCentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_centers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('title');
            $table->string('slug');
            $table->string('banner')->nullable()->comment('website image');
            $table->string('banner_mb')->nullable()->comment('mobile image');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('pincode')->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 10)->nullable();
            $table->decimal('longitude', 11, 10)->nullable();
            $table->text('email')->nullable();
            $table->text('contact_no')->nullable();
            $table->enum('status', ['published', 'unpublished']);
            $table->unsignedBigInteger('added_by');
            $table->integer( 'order_by' )->nullable();
            $table->foreign('added_by')->references('id')->on('users');
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
        Schema::dropIfExists('service_centers');
    }
}
