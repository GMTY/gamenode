<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Auth;

use App\Commands as Commands;
use App\Tournament_grids as Grid;

class MatсhController extends Controller
{

    /**
     * Функция получения результата игры по ID
     * Высчитывает победителей, выставляет результат
     *
     * Для дебага использовать
     * $match = 3642121420;
     * $account_id_capitan = 51503282;
     * $enemyAccountId = 128574881;
     */
    public function get_match($id) {
		$tournament_id = DB::table('tournaments')->max('id');
		$stage_id = DB::table('tournament_grids')
			->where('tournament_id', $tournament_id)
			->max('stage_id');

		$user_id = Auth::user()->id;
		$command = Auth::user()->command;

		$capitan = DB::table('commands')
			->where('capitan', $user_id)
			->get();

		if (count($capitan)) {

            $data = $this->getMatch($id);

            if (!isset($data->result->error)) {

                $account_id_capitan = $this->get_account_id(Auth::user()->steamid);

                $order = DB::table('tournament_grids')
                    ->where('command_id', $command)
                    ->where('tournament_id', $tournament_id)
                    ->where('stage_id', $stage_id)
                    ->first();
                // Порядок команды капитана, который вводи ID
                $order = $order->order;

                $parityCommand = $this->check_parity($order);
                if ($parityCommand) {
                    $opponentCommand = ++$order;
                } else {
                    $opponentCommand = --$order;
                }
                unset($order);

                $enemyCommand = Grid::where('order', $opponentCommand)
                    ->where('tournament_id', $tournament_id)
                    ->where('stage_id', $stage_id)
                    ->first();

                $enemyAccountId = Commands::where('id', $enemyCommand->command_id)
                    ->first();
                    
                $enemyAccountId = $this->get_account_id($enemyAccountId->capitan);

                for ($j = 0; $j < 11; $j++) {
                    if ($j === 10) {
                        return "Вы не играли в этом матче";
                    } else if ($data->result->players[$j]->account_id == $account_id_capitan) {
                        $game_slot = $j;
                        $j = 12;
                    }
                }

        
                for ($j = 0; $j < 11; $j++) {
                    if ($j === 10) {
                        return "Не совпадает команда, против которой должна была состояться игра";
                    } else if ($data->result->players[$j]->account_id == $enemyAccountId) {
                        $j = 12;
                    }
                }

                // Присваиваем значение 0 это светлая сторона, 1 это тёмная сторона
                if ($game_slot < 6) {
                    $command_1 = 0; //Light
                } else {
                    $command_1 = 1; //Dark
                }

                // Узнаём о полученных нами ранее данных по cURL какая сторона победила
                $result_match = $data->result->radiant_win;
                if ($result_match) {
                    $result_match = 0;
                } else {
                    $result_match = 1;
                }

                $order = Grid::where('command_id', $command)
                    ->where('tournament_id', $tournament_id)
                    ->where('stage_id', $stage_id)
                    ->first()->order;

                $number = $this->check_parity($order);
                if ($number) {
                    $order++;
                } else{
                    $order--;
                }

                if ($command_1 == $result_match) {

                    Grid::where('command_id', $command)
                        ->where('tournament_id', $tournament_id)
                        ->where('stage_id', $stage_id)
                        ->update(array(
                            'result' => true,
                            'game_id' => $id
                        ));
                    Grid::where('order', $order)
                        ->where('tournament_id', $tournament_id)
                        ->where('stage_id', $stage_id)
                        ->update(array(
                            'result' => false,
                            'game_id' => $id
                        ));
                    return 1;
                } else {

                    Grid::where('order', $command)
                        ->where('tournament_id', $tournament_id)
                        ->where('stage_id', $stage_id)
                        ->update(array(
                            'result' => false,
                            'game_id' => $id
                        ));

                    Grid::where('command_id', $order)
                        ->where('tournament_id', $tournament_id)
                        ->where('stage_id', $stage_id)
                        ->update(array(
                            'result' => true,
                            'game_id' => $id
                        ));
                    return 0;
                }
            } else {
                return "Матч не найден!";
            }
        } else {
			return "Не капитан";
		}
	}

	/**
	* @param id матча
	* @return json объект с данными о матче
     * Получаем данные о матче по id
	*/
	private function getMatch($id) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, "https://api.steampowered.com/IDOTA2Match_570/GetMatchDetails/V001/?match_id=$id&key=".env('STEAM_API_KEY'));
        $data = curl_exec($ch);
        curl_close($ch);
        return json_decode($data);
    }


	/**
	 * @param $steam_id
	 * @return string
	 * Возвращает accountID пользователя по его SteamID
	 */
	private function get_account_id($steam_id){
		if (strlen($steam_id) === 17) {
			$converted = substr($steam_id, 3) - 61197960265728;
		}
		else {
			$converted = '765'.($steam_id + 61197960265728);
		}
		return $converted;
	}


	/**
	 * @param $number int
	 * @return bool
	 * Функция определения чётности числа
	 * true - четное
	 * false - нечетное
	 */
	private function check_parity($number) {

		$number = $number % 2;

		if($number === 0){
			return true;
		}

		return false;
	}
}
