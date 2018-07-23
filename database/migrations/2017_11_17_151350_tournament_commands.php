<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TournamentCommands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Список регистраций команд на турнир. Далее из списка собирается турнирная сетка первого этапа
         */
        Schema::create('tournaments_commands', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tournament_id')->foreign()->references('id')->on('tournaments_info');
            $table->integer('command_id');
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
        Schema::dropIfExists('tournaments_commands');
    }
}
