<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeJobComment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_job_comment', function (Blueprint $table) {
            $table->id();
            $table->integer('job_id')->default(0)->comment('兼职id');
            $table->string('comment', 1000)->default('')->comment('评论内容');
            $table->tinyInteger('is_used')->default(0)->comment('是否被使用0=未被使用 1=已使用');
            $table->integer('task_job_id')->default(0)->comment('任务兼职id');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
            $table->index(['job_id']);
        });
        \DB::statement("ALTER TABLE `lt_part_time_job_comment` comment '兼职-App评价类型评论内容'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_job_comment');
    }
}
