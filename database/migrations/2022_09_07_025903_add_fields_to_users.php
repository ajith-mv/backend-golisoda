<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('image')->after('password')->nullable();
            $table->text('country_code')->after('image')->nullable();
            $table->string('mobile_no')->after('image')->nullable();
            $table->text('address')->after('mobile_no')->nullable();
            $table->unsignedBigInteger('role_id')->nullable()->after('address');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->longText('permissions')->nullable();
            $table->integer('status')->after('permissions')->default(1);
            $table->unsignedBigInteger('added_by')->after('status')->nullable();
            $table->foreign('added_by')->references('id')->on('users');
            $table->integer('is_super_admin')->default(0);
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->dropColumn('mobile_no');
            $table->dropColumn('address');
            $table->dropColumn('status');
            $table->dropColumn('permissions');
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->dropForeign(['added_by']);
            $table->dropColumn('added_by');
            $table->dropColumn('is_super_admin');
        });
    }
}
