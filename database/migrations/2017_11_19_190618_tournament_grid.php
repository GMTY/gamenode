<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TournamentGrid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournament_grids', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tournament_id')->foreign()->references('id')->on('tournaments_info');
            $table->integer('stage_id')->foreign()->references('id')->on('tournaments_stages');
            $table->integer('command_id');
            $table->integer('order');
            $table->integer('result')->nullable(); // 0 - проигрыш, 1 - победа, 2 - неявка на этап
            $table->string('lobby_id', 255)->nullable();
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
        Schema::dropIfExists('tournament_grids');
    }
}
