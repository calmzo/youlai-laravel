<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeWithdrawRecord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_withdraw_record', function (Blueprint $table) {
            $table->id();
            $table->string('withdraw_sn', 63)->default('')->comment('提现编号');
            $table->integer('user_id')->default(0)->comment('提现用户id');
            $table->integer('thread_id')->default(0)->comment('线索id');
            $table->decimal('amount')->default(0)->comment('提现金额');
            $table->decimal('withdraw_amount')->default(0)->comment('实际到账金额');
            $table->string('real_name')->default('')->comment('真实姓名');
            $table->string('phone')->default('')->comment('手机号');
            $table->tinyInteger('channel_type')->default(0)->comment('提现渠道类型');
            $table->decimal('withdraw_rate')->default(0)->comment('提现利率');
            $table->decimal('withdraw_interest')->default(0)->comment('提现利息');
            $table->integer('withdraw_time')->default(0)->comment('提现时间');
            $table->smallInteger('withdraw_status')->default(0)->comment('到账状态 0=打款中 1=打款成功 2=打款失败');
            $table->integer('operator_id')->default(0)->comment('操作人id');
            $table->integer('operator_type')->default(0)->comment('操作人类型 1=兼职宝账号 2=教之道后台账号');
            $table->string('operator_name')->default('')->comment('操作人名称');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
        });
        \DB::statement("ALTER TABLE `lt_part_time_withdraw_record` comment '兼职提现表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_withdraw_record');
    }
}
