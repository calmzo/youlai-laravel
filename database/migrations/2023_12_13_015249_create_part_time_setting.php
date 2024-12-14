<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartTimeSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_time_setting', function (Blueprint $table) {
            $table->id();
            $table->string('key')->default('')->comment('设置项的键');
            $table->text('value')->comment('设置项的值');
            $table->tinyInteger('type')->default(0)->comment('设置项的类型 1=提现设置');
        });
        \DB::statement("ALTER TABLE `lt_part_time_setting` comment '设置表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_time_setting');
    }
}
