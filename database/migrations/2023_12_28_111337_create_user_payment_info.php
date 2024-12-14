<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPaymentInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_payment_info', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0)->comment('用户id');
            $table->integer('third_id')->default(0)->comment('第三方id');
            $table->integer('payment_type')->default(0)->comment('收款方式 1=支付宝 2=微信');
            $table->string('real_name')->default('')->comment('收款真实姓名');
            $table->string('nick_name')->default('')->comment('昵称');
            $table->string('account_number')->default('')->comment('收款账号，根据不同的收款方式可能是银行账号、支付宝账号等  open_id');
            $table->string('extend_info', 1000)->default('')->comment('其他信息');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
        });
        \DB::statement("ALTER TABLE `lt_user_payment_info` comment '用户收款方式'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_payment_info');
    }
}
