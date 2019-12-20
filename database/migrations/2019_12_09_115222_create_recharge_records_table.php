<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRechargeRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recharge_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_sn')->comment('订单编号');
            $table->integer('access')->comment('充值账号');
            $table->integer('price')->comment('充值金额');
            $table->string('pay_type')->comment('支付方式');
            $table->integer('amount')->comment('支付金额');
            $table->tinyInteger('status')->comment('订单状态');
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
        Schema::dropIfExists('recharge_records');
    }
}
