<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeAdminMerchant extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_admin_merchant', function (Blueprint $table) {
            $table->id();
            $table->integer('admin_id')->default(0)->comment('账号id');
            $table->integer('merchant_id')->default(0)->comment('商户id');
        });
        \DB::statement("ALTER TABLE `lt_part_time_admin_merchant` comment '兼职账号商户关联表'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_admin_merchant');
    }
}
