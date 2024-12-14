<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeJobProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_job_product', function (Blueprint $table) {
            $table->id();
            $table->integer('job_id')->default(0)->comment('兼职id');
            $table->string('specifications', 1023)->default('')->comment('兼职规格值列表，采用JSON数组格式');
            $table->string('specification')->default('')->comment('兼职规格名称(直播日期)');
            $table->string('value')->default('')->comment('兼职规格值(直播时间)');
            $table->string('live_start_time')->default('')->comment('兼职直播开始时间');
            $table->string('live_end_time')->default('')->comment('兼职直播结束时间');
            $table->integer('live_start_time_unix')->default(0)->comment('兼职直播结束时间戳');
            $table->integer('live_end_time_unix')->default(0)->comment('兼职直播结束时间戳');
            $table->integer('number')->default(0)->comment('兼职货品数量');
            $table->decimal('price')->default(0)->comment('兼职货品价格');
            $table->string('url')->default('')->comment('兼职货品图片');
            $table->tinyInteger('live_in_room_type')->default(0)->comment('直播 进入直播间方式 1=链接方式 2=图文方式');
            $table->string('live_in_room_link', 1000)->default('')->comment('直播 进入直播间链接');
            $table->text('live_in_room_info')->nullable()->comment('直播-进入直播间图文信息');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
            $table->index(['job_id']);
        });
        \DB::statement("ALTER TABLE `lt_part_time_job_product` comment '兼职货品表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_job_product');
    }
}
