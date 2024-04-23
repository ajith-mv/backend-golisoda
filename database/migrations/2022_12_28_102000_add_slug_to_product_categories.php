<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlugToProductCategories extends Migration
{
   
    public function up()
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('parent_id');
        });
    }
    
    public function down()
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
}
