<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeComplaintTag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_complaint_tag', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('')->comment('投诉标签');
            $table->integer('complaint_type_id')->default(0)->comment('投诉类型id');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
        });
        \DB::statement("ALTER TABLE `lt_part_time_complaint_tag` comment '兼职投诉标签'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_complaint_tag');
    }
}
