<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeJobReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_job_report', function (Blueprint $table) {
            $table->id();
            $table->integer('job_id')->default(0)->comment('兼职id');
            $table->integer('user_id')->default(0)->comment('用户id');
            $table->string('job_name')->default('')->comment('兼职名称');
            $table->integer('report_time')->default(0)->comment('上报时间');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
        });
        \DB::statement("ALTER TABLE `lt_part_time_job_report` comment '兼职事件上报'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_job_report');
    }
}
