<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeTaskJobAuditRecord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_task_job_audit_record', function (Blueprint $table) {
            $table->id();
            $table->integer('task_id')->default(0)->comment('任务id');
            $table->integer('task_job_id')->default(0)->comment('任务兼职id');
            $table->tinyInteger('audit_status')->default(0)->comment('上传审核状态 0=未上传 1=已上传审核中 2=审核通过 3=审核拒绝');
            $table->string('content')->default('')->comment('提交审核内容');
            $table->string('img', 1000)->default('')->comment('提交审核的图片json数组');
            $table->string('refuse_remark')->default('')->comment('审核拒绝的原因');
            $table->integer('operator_id')->default(0)->comment('操作人id');
            $table->integer('operator_type')->default(0)->comment('操作人类型 1=兼职宝账号 2=教之道后台账号');
            $table->string('operator_name')->default('')->comment('操作人名称');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
        });
        \DB::statement("ALTER TABLE `lt_part_time_task_job_audit_record` comment '任务审核提交记录'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_task_job_audit_record');
    }
}
