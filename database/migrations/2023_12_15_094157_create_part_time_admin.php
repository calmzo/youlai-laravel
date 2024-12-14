<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_admin', function (Blueprint $table) {
            $table->id();
            $table->string('username', 24)->default('')->comment('登录账号用户名');
            $table->string('avatar', 500)->default('')->comment('头像');
            $table->string('mobile', 24)->default('')->comment('登录手机号');
            $table->string('nickname', 50)->default('')->comment('昵称');
            $table->string('name', 50)->default('')->comment('真实姓名');
            $table->string('password', 63)->default('')->comment('登录密码');
            $table->tinyInteger('is_can_pay')->default(0)->comment('是否可以支付0=否 1=是');
            $table->string('pay_password', 63)->default('')->comment('支付密码');
            $table->decimal('limit_pay_day_max')->default(0.00)->comment('当天支付最大金额 允许支付时有值');
            $table->string('department_name')->default('')->comment('打款人部门名称 允许支付时有值');
            $table->tinyInteger('is_open')->default(0)->comment('账号状态 0=关闭 1=开启');
            $table->integer('last_login_time')->default(0)->comment('最后登录时间');
            $table->string('last_login_ip', 63)->default('')->comment('最后登录ip');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
            $table->index(['username', 'mobile', 'nickname', 'name']);
        });
        \DB::statement("ALTER TABLE `lt_part_time_admin` comment '兼职账号表'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_admin');
    }
}
