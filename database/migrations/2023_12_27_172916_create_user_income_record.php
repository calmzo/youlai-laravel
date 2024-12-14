<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserIncomeRecord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_income_record', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0)->comment('用户id');
            $table->integer('task_job_id')->default(0)->comment('任务兼职id');
            $table->integer('task_id')->default(0)->comment('任务id');
            $table->integer('job_id')->default(0)->comment('兼职id');
            $table->decimal('amount')->default(0.00)->comment('到账金额');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
        });
        \DB::statement("ALTER TABLE `lt_user_income_record` comment '用户收款记录'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_income_record');
    }
}
