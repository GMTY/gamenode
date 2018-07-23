<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Commands as Commands;

use Auth;
use DB;
use Carbon\Carbon;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function check_join_btn() {

        $error = '';

        if (!Auth::user()) {
            return $error = 'Чтобы принять участие, необходимо авторизоваться';
        }

        if (!Auth::user()->command) {
            return $error = 'Чтобы принять участие, вы должны быть в команде';
        }

        $tournament = DB::table('tournaments')
            ->where('id', DB::table('tournaments')->max('id'))
            ->get();

        if($tournament[0]->status == 1) {
            return $error = 'Турнир завершён';
        }

        $tournament_commands = DB::table('tournaments_commands')
            ->where('tournament_id', $tournament[0]->id)
            ->count();

        $isReg = DB::table('tournaments_commands')
            ->where('tournament_id', $tournament[0]->id)
            ->where('command_id', Auth::user()->command)
            ->first();

        if (isset($isReg)) {
            return $error = 'Вы уже в турнире';
        }

        $now  = Carbon::now();

        if ($tournament[0]->start > $now) {
            return $error = 'Регистрация на турнир еще не началась';
        }
        if ($tournament[0]->end < $now) {
            return $error = 'Регистрация на турнир закончилась';
        }

        $checkCapitan = Commands::where('capitan', Auth::user()->id)->first();
        if (!isset($checkCapitan)) {
            return $error = 'Команду на турнир может регестрировать только капитан';
        }

        $command_info = DB::table('commands')
            ->where('id', Auth::user()->command)
            ->get();

        if ($command_info[0]->status == 0) {
            return $error = 'Ваша команда не допускается к участию';
        }

        $count_teammates = DB::table('users')
            ->where('command', '>', 0)
            ->where('command', Auth::user()->command)
            ->get();

        if (count($count_teammates) < env('MIN_COMMAND_POPULATION')) {
            return $error = 'В команде должно быть не меньше '.env('MIN_COMMAND_POPULATION').' человек';
        }

        if ($tournament_commands >= env('MAX_COUNT_COMMAND')) {
            return $error = 'Места в турнире закончились';
        }

        if ($command_info[0]->balance < env('TOURNAMENT_COST')) {
            return $error = 'У команды недостаточно средств для участия';
        }

        return $error;
    }


}
