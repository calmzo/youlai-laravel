<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeMerchantRecharge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_merchant_recharge', function (Blueprint $table) {
            $table->id();
            $table->string('recharge_sn', 63)->default('')->comment('充值编号');
            $table->integer('merchant_id')->default(0)->comment('商户id');
            $table->string('merchant_name')->default('')->comment('商户名称');
            $table->integer('operator_id')->default(0)->comment('操作人id');
            $table->tinyInteger('operator_type')->default(0)->comment('操作人类型');
            $table->decimal('amount')->default(0.00)->comment('金额');
            $table->integer('recharge_time')->default(0)->comment('充值时间');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
        });
        \DB::statement("ALTER TABLE `lt_part_time_merchant_recharge` comment '兼职商户充值表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_merchant_recharge');
    }
}
