<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_users', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('access_key')->comment('访问密钥key');
            $table->string('secret_key')->comment('密钥');
            $table->string('type')->comment('类型');
            $table->string('name')->comment('名称');
            $table->string('uri')->comment('回调地址');
            $table->integer('balance')->default(0)->comment('余额 1rmb = 1000');
            $table->text('options')->comment('其他配置');
            $table->tinyInteger('status')->default(\App\Models\AccessUser::STATUS_NORMAL)->comment('状态');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_users');
    }
}
