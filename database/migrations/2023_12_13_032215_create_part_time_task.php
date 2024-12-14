<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeTask extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_task', function (Blueprint $table) {
            $table->id();
            $table->string('task_sn', 63)->default('')->comment('任务编号');
            $table->tinyInteger('task_type')->default(0)->comment('任务类型 1=认证 2=APP评价 3=直播互动 4=其他');
            $table->integer('user_id')->default(0)->comment('学员id');
            $table->integer('thread_id')->default(0)->comment('线索id');
            $table->integer('job_id')->default(0)->comment('兼职id');
            $table->smallInteger('task_status')->default(0)->comment('任务状态');
            $table->decimal('task_price')->default(0.00)->comment('任务总费用');
            $table->integer('join_time')->default(0)->comment('领取任务时间');
            $table->integer('finish_time')->default(0)->comment('完成任务时间');
            $table->integer('limit_finish_time')->default(0)->comment('限制完成任务时间 不限制填0');
            $table->text('content')->comment('兼职详情');
            $table->text('operate_step')->comment('操作步骤');
            $table->integer('payment_time')->default(0)->comment('打款时间');
            $table->decimal('payment_price')->default(0.00)->comment('打款金额');
            $table->integer('payment_admin_id')->default(0)->comment('打款人id');
            $table->tinyInteger('payment_admin_type')->default(0)->comment('打款人类型');
            $table->string('cancel_remark')->default('')->comment('取消时的备注');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
        });
        \DB::statement("ALTER TABLE `lt_part_time_task` comment '学员兼职任务表'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_task');
    }
}
