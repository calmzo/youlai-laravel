<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserThirdAuth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_third_auth', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0)->comment('用户id');
//            $table->integer('auth_id')->default(0)->comment('授权id');
            $table->string('openid')->default('')->comment('第三方用户唯一标识');
            $table->integer('auth_type')->default(0)->comment('第三方平台唯一标识 1=微信 2=支付宝');
            $table->string('access_token')->default('')->comment('第三方获取的access_token,校验使用');
            $table->string('nick_name')->default('')->comment('昵称');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
        });
        \DB::statement("ALTER TABLE `lt_user_third_auth` comment '第三方用户授权信息'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_third_auth');
    }
}
