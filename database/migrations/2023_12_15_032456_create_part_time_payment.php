<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimePayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_payment', function (Blueprint $table) {
            $table->id();
            $table->string('payment_sn', 63)->default('')->comment('打款编号');
            $table->integer('user_id')->default(0)->comment('用户id');
            $table->integer('thread_id')->default(0)->comment('线索id');
            $table->integer('job_id')->default(0)->comment('兼职id');
            $table->string('job_name')->default('')->comment('兼职名称');
            $table->integer('merchant_id')->default(0)->comment('商户id');
            $table->string('merchant_name')->default('')->comment('商户名称');
            $table->integer('task_id')->default(0)->comment('任务id');
            $table->integer('task_job_id')->default(0)->comment('任务兼职id');
            $table->decimal('price')->default(0.00)->comment('单价');
            $table->integer('finish_time')->default(0)->comment('完成时间');
            $table->decimal('payment_price')->default(0.00)->comment('打款金额');
            $table->integer('payment_time')->default(0)->comment('打款时间');
            $table->integer('payment_admin_id')->default(0)->comment('打款人id');
            $table->string('payment_admin_name')->default('')->comment('打款人岷城');
            $table->tinyInteger('payment_admin_type')->default(0)->comment('打款人类型');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
        });
        \DB::statement("ALTER TABLE `lt_part_time_payment` comment '打款表'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_payment');
    }
}
