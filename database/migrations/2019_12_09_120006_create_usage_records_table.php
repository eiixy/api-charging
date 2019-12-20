<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsageRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usage_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('access')->index()->comment('账号');
            $table->integer('api')->index()->comment('接口');
            $table->string('method')->comment('请求方法');
            $table->string('uri')->comment('请求地址');
            $table->string('options')->comment('附加数据');
            $table->string('ip')->comment('ip地址');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usage_records');
    }
}
