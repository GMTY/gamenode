<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments_history', function (Blueprint $table) {
            $table->increments('id');
            $table->string('trn_id', 30); // ID транзакции в кабинете QIWI
            $table->integer('amount')->default(0);
            $table->integer('user_id');
            $table->integer('command_id');
            $table->string('status', 30)->default('NULL'); // paid - оплачен, waiting - ожидает, rejected - отклонен, unpaid - ошибка при оплате, expired - счет просрочен (https://developer.qiwi.com/ru/pull-payments/index.html#refund_status)
            $table->timestamp('date')->default(DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('payments_history');
    }
}
