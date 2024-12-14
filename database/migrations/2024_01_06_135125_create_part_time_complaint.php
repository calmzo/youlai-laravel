<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeComplaint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_complaint', function (Blueprint $table) {
            $table->id();
            $table->integer('job_id')->default(0)->comment('兼职id');
            $table->integer('complaint_type_id')->default(0)->comment('投诉类型id');
            $table->string('complaint_tag_ids')->default(0)->comment('投诉标签ids多选');
            $table->string('complaint_tag_titles')->default(0)->comment('投诉标签多选');
            $table->string('content', 1000)->default('')->comment('投诉说明');
            $table->string('img', 1000)->default(0)->comment('投诉说明照片json');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
        });
        \DB::statement("ALTER TABLE `lt_part_time_complaint` comment '兼职投诉表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_complaint');
    }
}
