<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProfileStat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Таблица с сохранением результатов всех матчей игрока
         */
        Schema::create('profile_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->foreign()->references('id')->on('users');
            $table->integer('opponent_id');
            $table->integer('side'); // сторона, на которой играл игрок
            $table->integer('result');
            $table->timestamp('date');
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
        Schema::dropIfExists('profile_stats');
    }
}
