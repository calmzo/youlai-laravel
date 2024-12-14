<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeJobAppReview extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_job_app_review', function (Blueprint $table) {
            $table->id();
            $table->integer('job_id')->default(0)->comment('兼职id');
            $table->tinyInteger('phone_type')->default(0)->comment('APP评价 机型 1=华为 2=荣耀');
            $table->integer('phone_limit_day')->default(0)->comment('APP评价 机型几天内');
            $table->integer('phone_limit_count')->default(0)->comment('APP评价 机型评价次数');
            $table->integer('limit_complete_duration_h')->default(0)->comment('APP评价 限制完成时');
            $table->integer('limit_complete_duration_i')->default(0)->comment('APP评价 限制完成分');
            $table->integer('limit_complete_duration_s')->default(0)->comment('APP评价 限制完成秒');
            $table->text('comment')->comment('APP评价分配评论');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
        });
        \DB::statement("ALTER TABLE `lt_part_time_job_app_review` comment '兼职-App评价认证类型子表'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_job_app_review');
    }
}
