<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeTaskJob extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_task_job', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0)->comment('学员id');
            $table->integer('thread_id')->default(0)->comment('线索id');
            $table->integer('task_id')->default(0)->comment('任务id');
            $table->tinyInteger('task_job_type')->default(0)->comment('任务类型 1=认证 2=APP评价 3=直播互动 4=其他');
            $table->smallInteger('task_job_status')->default(0)->comment('任务兼职状态');
            $table->integer('job_id')->default(0)->comment('兼职id');
            $table->string('job_name')->default('')->comment('兼职名称');
            $table->string('job_sn')->default('')->comment('兼职编号');
            $table->integer('product_id')->default(0)->comment('兼职货品id');
            $table->smallInteger('number')->default(0)->comment('兼职货品购买数量');
            $table->decimal('price')->default(0.00)->comment('兼职货品的售价');
            $table->string('job_tag', 500)->default('')->comment('兼职标签 json格式');
            $table->tinyInteger('job_unit')->default(0)->comment('兼职单位');
            $table->string('pic_url')->default('')->comment('任务图片');
            $table->string('specifications')->default('')->comment('商品货品的规格列表');
            $table->string('specification')->default('')->comment('兼职规格名称(直播日期)');
            $table->string('value')->default('')->comment('兼职规格值(直播时间)');
            $table->integer('comment_id')->default(0)->comment('App评价类型时分配的的评论id');
            $table->string('comment_content')->default('')->comment('App评价类型时分配的的评论内容');
            $table->string('wechat_number')->default('')->comment('联系微信号');
            $table->tinyInteger('is_follow')->default(0)->comment('是否跟进处理0=待处理 1=已处理');
            $table->tinyInteger('audit_status')->default(0)->comment('上传审核状态 0=未上传 1=已上传审核中 2=审核通过 3=审核拒绝');
            $table->integer('audit_time')->default(0)->comment('提交审核时间');
            $table->string('refuse_remark')->default('')->comment('拒绝原因');
            $table->tinyInteger('is_payment')->default(0)->comment('是否打款 0=未打款 1=已打款');
            $table->integer('finish_time')->default(0)->comment('完成任务时间');
            $table->string('cancel_remark')->default('')->comment('取消时的备注');
            $table->integer('payment_time')->default(0)->comment('打款时间');
            $table->decimal('payment_price')->default(0.00)->comment('打款金额');
            $table->integer('payment_admin_id')->default(0)->comment('打款人id');
            $table->tinyInteger('payment_admin_type')->default(0)->comment('打款人类型');
            $table->string('payment_admin_name')->default('')->comment('打款人名称');
            $table->integer('follow_admin_id')->default(0)->comment('跟进人id');
            $table->integer('follow_time')->default(0)->comment('跟进时间');
            $table->tinyInteger('follow_admin_type')->default(0)->comment('跟进人类型');
            $table->string('follow_admin_name')->default('')->comment('跟进人名称');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
            $table->index('task_id');
            $table->index('job_id');
        });
        \DB::statement("ALTER TABLE `lt_part_time_task_job` comment '学员任务兼职表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_task_job');
    }
}
