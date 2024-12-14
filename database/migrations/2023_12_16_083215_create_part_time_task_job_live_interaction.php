<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeTaskJobLiveInteraction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_task_job_live_interaction', function (Blueprint $table) {
            $table->id();
            $table->integer('task_id')->default(0)->comment('任务id');
            $table->integer('task_job_id')->default(0)->comment('任务兼职id');
            $table->integer('job_id')->default(0)->comment('兼职id');
            $table->integer('product_id')->default(0)->comment('兼职货品id');
            $table->string('platform_type')->default('')->comment('直播平台类型');
            $table->string('platform_name')->default('')->comment('直播平台名称');
            $table->string('platform_nickname')->default('')->comment('直播平台昵称');
            $table->string('live_date')->default('')->comment('兼职直播日期');
            $table->string('live_time')->default('')->comment('兼职直播时间');
            $table->string('live_start_time')->default('')->comment('兼职直播开始时间');
            $table->string('live_end_time')->default('')->comment('兼职直播结束时间');
            $table->integer('live_start_time_unix')->default(0)->comment('兼职直播结束时间戳');
            $table->integer('live_end_time_unix')->default(0)->comment('兼职直播结束时间戳');
            $table->tinyInteger('live_in_room_type')->default(0)->comment('直播 进入直播间方式 1=链接方式 2=图文方式');
            $table->string('live_in_room_link', 1000)->default('')->comment('直播 进入直播间链接');
            $table->text('live_in_room_info')->nullable()->comment('直播-进入直播间图文信息');
            $table->string('live_shortlink', 1000)->nullable()->comment('开播链接');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
        });
        \DB::statement("ALTER TABLE `lt_part_time_task_job_live_interaction` comment '任务兼职直播类型子表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_task_job__live__interaction');
    }
}
