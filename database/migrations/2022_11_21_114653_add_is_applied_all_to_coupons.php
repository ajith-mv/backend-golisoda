<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsAppliedAllToCoupons extends Migration
{
    
    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->enum('is_applied_all', ['yes', 'no'])->after('coupon_type')->nullable();
        });
    }

    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('is_applied_all');
        });
    }
    
}
