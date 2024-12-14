<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserListExternal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('user_list_external', function (Blueprint $table) {
            if (!Schema::hasColumn('user_list_external', 'balance')) {
                $table->decimal('balance')->default(0.00)->comment('用户余额');
            }
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('user_list_external', function (Blueprint $table) {
            if (Schema::hasColumn('user_list_external', 'balance')) {
                $table->dropColumn(['balance']);
            }
        });
    }
}
