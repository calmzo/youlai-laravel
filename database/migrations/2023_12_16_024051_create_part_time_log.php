<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_log', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0)->comment('id');
            $table->string('username')->default(0)->comment('名称');
            $table->string('message')->default('')->comment('信息');
            $table->string('status_code')->default('')->comment('status_code');
            $table->string('method')->default('')->comment('method');
            $table->string('path')->default('')->comment('path');
            $table->string('permission')->default('')->comment('permission');
            $table->integer('create_time');
            $table->integer('update_time');
            $table->integer('delete_time')->default(0);
        });
        \DB::statement("ALTER TABLE `lt_part_time_log` comment '操作日志'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_log');
    }
}
