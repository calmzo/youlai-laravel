<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeTaskJobAppReview extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_task_job_app_review', function (Blueprint $table) {
            $table->id();
            $table->integer('task_id')->default(0)->comment('任务id');
            $table->integer('task_job_id')->default(0)->comment('任务兼职id');
            $table->integer('job_id')->default(0)->comment('兼职id');
            $table->integer('product_id')->default(0)->comment('兼职货品id');
            $table->integer('comment_id')->default(0)->comment('评论id');
            $table->string('comment_content')->default('')->comment('评论内容');
            $table->text('app_review_comment_img')->nullable()->comment('评论截图');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
        });
        \DB::statement("ALTER TABLE `lt_part_time_task_job_app_review` comment '任务App评价认证类型子表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_task_job_app_review');
    }
}
