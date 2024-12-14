<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeTaskUserDynamicLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_task_user_dynamic_log', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0)->comment('用户id');
            $table->integer('task_job_id')->default(0)->comment('任务兼职id');
            $table->integer('type')->default(0)->comment('动态类型');
            $table->integer('operator_id')->default(0)->comment('操作人id');
            $table->integer('operator_id_type')->default(0)->comment('操作人类型 1=兼职宝账号 2=教之道后台账号');
            $table->string('operator_name')->default('')->comment('操作人名称');
            $table->integer('operator_type')->default(0)->comment('操作人类型 1=兼职人员 2=业务人员');
            $table->integer('operator_time')->default(0)->comment('操作时间');
            $table->string('message')->default('')->comment('动态信息');
            $table->string('pic_url', 1000)->default('')->comment('动态信息图片');
            $table->string('extend_info', 1000)->default('')->comment('其他信息 json数组');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
        });
        \DB::statement("ALTER TABLE `lt_part_time_task_user_dynamic_log` comment '兼职动态信息表'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_task_user_dynamic_log');
    }
}
