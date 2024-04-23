<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGalleryPathToProductImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->text('gallery_path')->nullable()->after('image_path');
            $table->text('image_path')->nullable(true)->change();
            $table->text('preview_path')->nullable()->after('gallery_path');
            $table->string('file_size')->nullable()->after('preview_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropColumn('gallery_path');
            $table->dropColumn('preview_path');
            $table->dropColumn('file_size');
            $table->text('image_path')->nullable(false)->change();
        });
    }
}
