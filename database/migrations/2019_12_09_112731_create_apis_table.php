<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('method')->comment('请求方法');
            $table->string('uri')->comment('请求地址');
            $table->string('desc')->comment('接口说明');
            $table->integer('price')->comment('接口价格  1rmb = 1000');
            $table->timestamps();

            $table->unique(['method','uri']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apis');
    }
}
