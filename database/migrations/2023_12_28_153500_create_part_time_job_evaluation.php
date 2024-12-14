<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeJobEvaluation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_job_evaluation', function (Blueprint $table) {
            $table->id();
            $table->integer('job_id')->default(0)->comment('兼职id');
            $table->integer('task_id')->default(0)->comment('任务id');
            $table->integer('user_id')->default(0)->comment('用户id');
            $table->integer('task_job_id')->default(0)->comment('任务兼职id');
            $table->integer('phone_type')->default(0)->comment('报名机型');
            $table->string('phone_brand')->default('')->comment('报名手机品牌');
            $table->integer('phone_ver')->default(0)->comment('报名手机型号');
            $table->integer('join_time')->default(0)->comment('报名时间');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
        });
        \DB::statement("ALTER TABLE `lt_part_time_job_evaluation` comment '兼职报名机型记录'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_job_evaluation');
    }
}
