<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeJob extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_job', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('')->comment('兼职名称');
            $table->string('job_sn', 63)->default('')->comment('兼职编号');
            $table->decimal('price')->default(0.00)->comment('兼职单价');
            $table->tinyInteger('unit')->default(0)->comment('兼职单位');
            $table->string('tag', 500)->default('')->comment('兼职标签 json格式');
            $table->integer('limit_quota_max')->default(0)->comment('兼职名额限制');
            $table->integer('stock')->default(0)->comment('库存');
            $table->integer('join_number')->default(0)->comment('兼职报名人数');
            $table->tinyInteger('type')->default(0)->comment('兼职类型 1=认证 2=APP评价 3=直播互动 4=其他');
            $table->integer('merchant_id')->default(0)->comment('兼职商户id');
            $table->text('content')->comment('兼职详情');
            $table->text('operate_step')->comment('操作步骤');
            $table->tinyInteger('is_on_sale')->default(0)->comment('手否上架 0=下架 1=上架');
            $table->tinyInteger('is_show')->default(0)->comment('是否显示 0=不显示 1=显示');
            $table->smallInteger('sort_order')->default(100)->comment('排序');
            $table->integer('pv')->default(0)->comment('浏览次数pv');
            $table->integer('uv')->default(0)->comment('浏览人数uv');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
            $table->index(['name', 'job_sn']);

        });
        \DB::statement("ALTER TABLE `lt_part_time_job` comment '兼职表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_job');
    }
}
