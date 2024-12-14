<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeTaskJobRuleImg extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_task_job_rule_img', function (Blueprint $table) {
            $table->id();
            $table->integer('task_id')->default(0)->comment('任务id');
            $table->integer('user_id')->default(0)->comment('用户id');
            $table->integer('task_job_id')->default(0)->comment('任务兼职id');
            $table->string('name')->default('')->comment('文件名称');
            $table->string('path')->default('')->comment('文件路径');
            $table->string('ext')->default('')->comment('文件后缀');
            $table->string('size')->default('')->comment('文件大小，字节数，单位B');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
        });
        \DB::statement("ALTER TABLE `lt_part_time_task_job_rule_img` comment '任务兼职上传图片'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_task_job_rule_img');
    }
}
