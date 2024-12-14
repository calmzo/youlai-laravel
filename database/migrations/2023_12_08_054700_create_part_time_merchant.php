<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeMerchant extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_merchant', function (Blueprint $table) {
            $table->id();
            $table->string('logo_url', 500)->default('')->comment('log图片地址');
            $table->string('name', 50)->default('')->comment('商户名称');
            $table->decimal('balance')->default(0.00)->comment('商户余额');
//            $table->integer('publish_number')->default(0)->comment('发布数量');
            $table->tinyInteger('is_open')->default(0)->comment('商户状态 0=关闭 1=开启');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
            $table->index(['name']);
        });
        \DB::statement("ALTER TABLE `lt_part_time_merchant` comment '兼职商户表'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_merchant');
    }
}
