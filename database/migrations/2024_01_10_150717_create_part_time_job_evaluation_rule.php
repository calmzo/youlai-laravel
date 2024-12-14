<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeJobEvaluationRule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_job_evaluation_rule', function (Blueprint $table) {
            $table->id();
            $table->integer('job_id')->default(0)->comment('兼职id');
            $table->integer('task_id')->default(0)->comment('任务id');
            $table->integer('user_id')->default(0)->comment('用户id');
            $table->integer('phone_type')->default(0)->comment('报名机型');
            $table->string('phone_brand')->default('')->comment('报名手机品牌');
            $table->string('phone_ver')->default('')->comment('报名手机型号');
            $table->integer('limit_time_start')->default(0)->comment('限制时间开始');
            $table->integer('limit_time_end')->default(0)->comment('限制时间结束');
            $table->integer('limit_count')->default(0)->comment('限制次数');
            $table->integer('join_count')->default(0)->comment('报名次数');
            $table->integer('is_rule')->default(0)->comment('是否有效 1=有效');
            $table->integer('is_limit')->default(0)->comment('是否限制 1=限制');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_job_evaluation_rule');
    }
}
