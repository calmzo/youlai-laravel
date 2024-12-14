<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeJobLiveInteraction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_job_live_interaction', function (Blueprint $table) {
            $table->id();
            $table->integer('job_id')->default(0)->comment('兼职id');
            $table->tinyInteger('platform_type')->default(0)->comment('平台类型 1=抖音 2=bilibili');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
        });
        \DB::statement("ALTER TABLE `lt_part_time_job_live_interaction` comment '兼职-直播类型子表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_job_live_interaction');
    }
}
