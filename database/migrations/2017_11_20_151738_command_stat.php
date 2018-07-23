<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CommandStat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('command_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('command_id')->foreign()->references('id')->on('commands');
            $table->integer('win_matches');
            $table->integer('all_matches');
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
        Schema::dropIfExists('command_stats');
    }
}
