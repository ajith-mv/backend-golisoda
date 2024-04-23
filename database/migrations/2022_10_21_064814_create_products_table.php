<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string( 'product_name' )->fullText();
            // $table->fullText( 'product_name' );
            $table->string( 'hsn_code' )->nullable();
            $table->string( 'product_url' );
            $table->string( 'sku' );
            $table->double( 'price', 12,2 );
            $table->double( 'sale_price', 12,2 );
            $table->date( 'sale_start_date' )->nullable();
            $table->date( 'sale_end_date' )->nullable();
            $table->enum( 'status', ['published', 'unpublished']);
            $table->enum( 'has_video_shopping', ['yes', 'no']);
            $table->enum( 'stock_status', ['in_stock', 'out_of_stock', 'coming_soon']);
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('tag_id')->nullable();
            $table->unsignedBigInteger('label_id')->nullable();
            $table->tinyInteger('is_display_home')->default(0);
            $table->tinyInteger('is_featured')->default(0);
            $table->tinyInteger('is_best_selling')->default(0);
            $table->tinyInteger('is_new')->default(0);
            $table->integer('tax_id')->nullable();
            $table->longText( 'description' )->nullable();
            $table->longText( 'technical_information' )->nullable();
            $table->longText( 'feature_information' )->nullable();
            $table->longText( 'specification' )->nullable();
            $table->longText( 'brochure_upload' )->nullable();
            $table->longText( 'base_image' )->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('added_by');
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
        Schema::dropIfExists('products');
    }
}
