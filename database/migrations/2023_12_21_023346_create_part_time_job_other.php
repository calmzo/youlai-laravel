<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeJobOther extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_job_other', function (Blueprint $table) {
            $table->id();
            $table->integer('job_id')->default(0)->comment('兼职id');
            $table->tinyInteger('other_is_rule_content_required')->default(0)->comment('是否填写规范文字');
            $table->string('other_rule_content', 1000)->default('')->comment('规范文字json数组');
            $table->tinyInteger('other_is_rule_img_required')->default(0)->comment('是否上传规定图片');
            $table->string('other_rule_img_title')->default('')->comment('规定图片标题');
            $table->text('other_rule_img')->nullable()->comment('规定图片json信息数组');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
        });
        \DB::statement("ALTER TABLE `lt_part_time_job_other` comment '兼职-其他类型子表'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_job_other');
    }
}
