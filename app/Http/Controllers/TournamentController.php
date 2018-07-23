<?php

namespace App\Http\Controllers;

use App\Tournament_grids;
use App\Tournament_stages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\News as News;
use App\Commands as Commands;
use App\User as User;
use App\Tournaments as Tournaments;
use App\Tournament_stages as Stage;
use App\Tournaments_commands as Teams;
use App\Tournament_grids as Grid;
use App\Http\Controllers\MatсhController as Match;

use Auth;
use Carbon\Carbon;

class TournamentController extends Controller{



    public function __construct() {
        $user = User::where('id', 129)->first();
        Auth::login($user, true);
    }
    /**
     * @param int $id - ID турнира
     * @return mixed
     * Возвращает страницу с турнирной сеткой
     */
    public function index($id = 0){
        $prize = 0;
        $time_diff = 0;

        $id = $this->checkMaxTournamentId($id);
        if ($id == -1) {
            return redirect('/tournament');
        }

        $count_stages = DB::table('tournament_grids')
            ->where('tournament_id', $id)
            ->max('stage_id');

        $data = array();

        // формируем массив команд, разбитый по этапам
        for ($i = 0; $i < $count_stages; $i++) {

            $data[$i] = DB::table('tournament_grids')
                ->where('tournament_id', '=', $id)
                ->where('stage_id', '=', $i + 1)
                ->join('commands', function ($join) {

                    $join->on('commands.id', '=', 'tournament_grids.command_id');

                })->get();

            /* дополняем массив команд этапа пустыми командами до степени 2-ки */
            if ($toCount = $this->isNeedCompleteNet($data[$i])) {

                for ($j = count($data[$i]); $j < $toCount; $j++) {

                    $emptyCommand = (object) array (
                        'id' => 0,
                        'name' => 'Нет команды',
                        'result' => 0,
                        'order' => $j,
                        'avatar' => ''
                    );

                    $data[$i][] = $emptyCommand;
                }

            }

        }

        $info = DB::table('tournaments')
            ->where('id', $id)
            ->first();

        $countPlayers = DB::table('tournaments_commands')
            ->where('tournament_id', DB::table('tournaments')->max('id'))
            ->count();

        if($countPlayers) {
            $prize = $countPlayers * env('PRIZE_FACTOR') * env('TOURNAMENT_COST');
        }

        $stage_titles = DB::table('tournament_stages')
            ->where('tournament_id', $id)
            ->orderBy('stage')
            ->get();

        $error = $this->check_join_btn();

        $disabled = '';
        if ($error) {
            $disabled = "disabled";
        }

        $currentStage = DB::table('tournament_grids')
            ->where('tournament_id', DB::table('tournaments')->max('id'))
            ->max('stage_id');

        if(Auth::user()){

            $command_id = Auth::user()->command;

            if($currentStage) {
    
                $order = DB::table('tournament_grids')
                    ->join('commands', 'commands.id', '=', 'tournament_grids.command_id')
                    ->where('tournament_id', DB::table('tournaments')->max('id'))
                    ->where('stage_id', $currentStage)
                    ->where('command_id', $command_id)
                    ->first();

                if($order !== NULL){
                    $order = $order->order;

                    $check_parity = $this->check_parity($order);
                    if($check_parity) {
                        $order++;
                    }else {
                        $order--;
                    }

                    $command_enemy_id = DB::table('tournament_grids')
                        ->join('commands', 'commands.id', '=', 'tournament_grids.command_id')
                        ->where('tournament_id', DB::table('tournaments')->max('id'))
                        ->where('stage_id', $currentStage)
                        ->where('order', $order)
                        ->where('result', '=', NULL)
                        ->first();

                    // если у соперников не стоимт result в турнирной сетке, то мы должны показать блок "предстоит игра с командой"
                    if ($command_enemy_id) {

                        $command_enemy = DB::table('users')
                            ->join('commands','commands.id', '=', 'users.command')
                            ->where('command', $command_enemy_id->command_id)
                            ->select('users.id','commands.name','users.username')
                            ->get();

                        if (!count($command_enemy)) {
                            $command_enemy = 0;
                        }

                        $currentStageDate = DB::table('tournament_stages')
                            ->where('tournament_id', DB::table('tournaments')->max('id'))
                            ->where('stage', $currentStage)
                            ->first()->date;

                        $time = strtotime($currentStageDate);
                        $time_now = time();
                        $time_diff = $time - $time_now;

                    }

                }
            }
        }

        if (!isset($command_enemy)) {
            // отключаем блок "предстоит игра с командой"
            $command_enemy = 0;
        }

        return view('tournament.index',
            [
                'commands' => $data,
                'info' => $info,
                'prize' => $prize,
                'title' => $info->title,
                'stages' => $stage_titles,
                'join_error' => $error,
                'join_disabled' => $disabled,
                'enemy_command' => $command_enemy,
                'time' => $time_diff,
                'currentStage' => $currentStage
            ]
        );
    }

    /**
     * @param $arr
     * @return int
     * Рассчитываем до какого количества нужно заполнить массив, чтобы была степень 2-ки
     */
    private function isNeedCompleteNet($arr) {
        $count = count($arr);

        if ($count > 2 and $count < 4 ) {
            return 4;
        }
        if ($count > 4 and $count < 8 ) {
            return 8;
        }
        if ($count > 8 and $count < 16 ) {
            return 16;
        }
        if ($count > 16 and $count < 32 ) {
            return 32;
        }
        if ($count > 32 and $count < 64 ) {
            return 64;
        }
        if ($count > 64 and $count < 128 ) {
            return 128;
        }

        return 0;
    }

    /**
     * @return mixed
     * Возвращает страницу с расписанием этапов турнира
     */
    public function schedule($id = 0){

        $id = $this->checkMaxTournamentId($id);
        if ($id == -1) {
            return redirect('/tournament');
        }

        $prize = 0;

        $tournament = DB::table('tournaments')
            ->where('id', $id)
            ->get();

        // даты регистрации
        $start = $tournament[0]->start;
        $end = $tournament[0]->end;

        $currentStageNumber = -2; // момент до начала регистрации на турнир

        $now = date("Y-m-d H:i:s");
        if($start < $now && $now < $end) {
            $currentStageNumber = -1;
        }else if($start < $now && $end < $now) {
            $currentStage = DB::table('tournament_stages')
                ->where('tournament_id', $id)
                ->where('date','<',$now)
                ->max('date');
            
            if($currentStage){
                $currentStageNumber = DB::table('tournament_stages')
                    ->where('tournament_id', $id)
                    ->where('date','=',$currentStage)
                    ->first()->stage; 
            }else if($currentStage === NULL){
                $currentStageNumber = 0;
            }
        }


        $stages = DB::table('tournament_stages')
            ->where('tournament_id', $id)
            ->orderBy('stage')
            ->get();

        $data = DB::table('tournaments_commands')
            ->where('tournaments_commands.tournament_id', $id)
            ->join('commands', 'commands.id', '=', 'tournaments_commands.command_id')
            ->leftJoin('users', 'users.command', '=', 'commands.id')
            ->select('commands.*', DB::raw('count(users.command) as members'))
            ->groupBy('commands.id')
            ->get();

        $count = $data->count();

        if ($count) {
            $prize = $count * env('PRIZE_FACTOR') * env('TOURNAMENT_COST');
        }
        else {
            $data = array();
        }

        $error = $this->check_join_btn();

        $disabled = '';
        if ($error) {
            $disabled = "disabled";
        }

        return view('tournament.schedule',
            [
                'commands' => $data,
                'count' => $count,
                'prize' => $prize,
                'stages' => $stages,
                'title' => $tournament[0]->title,
                'reg_start' => $start,
                'reg_end' => $end,
                'join_error' => $error,
                'join_disabled' => $disabled,
                'current_stage' => $currentStageNumber
            ]
        );
    }


    /**
     * @param int $id
     * @return mixed
     * Проверяем существование ID турнира в базе.
     * Если нет, то редирект на страницу турнира
     */
    private function checkMaxTournamentId($id = 0) {
        $max_tournament_id = DB::table('tournaments')->max('id');

        if (!$id) {
            $id = $max_tournament_id;
        }

        if ($id > $max_tournament_id) {
            //TODO Как переделать на нормальный редирект на страницу tournament?
            $id = -1;
        }

        return $id;
    }

    // Добавляет комманду на участие в турнире
    public function join(){

        if(!Auth::user()) {
            return redirect('auth/steam');
        }

        $id_user = Auth::user()->id;
        $command = Commands::where('capitan', $id_user)->first();

        if (isset($command) && $command !== NULL) {

            $balance = $command->balance;
            $status = $command->status;
            $count = User::where('command', $command->id)->count();

            $tournamentId = DB::table('tournaments')
                ->where('status', '0')->max('id');

            $check_team = Teams::where('command_id', $command->id)
                ->where('tournament_id', $tournamentId)
                ->first();

            $info_tournament = Tournaments::where('id', DB::table('tournaments')->max('id'))
                ->first();

            $start = $info_tournament->start;
            $now = date("Y-m-d H:i:s");
            $end = $info_tournament->end;

            if(isset($check_team->command_id)) {
                return view('tournament/join', [
                    'message' => 'Вы уже зарегистрированы на турнир'
                ]);
            }



            if ($now < $start) {
                return view('tournament/join', [
                    'message' => 'Регистрации еще не началась'
                ]);
            }

            if($count >= env('MIN_COMMAND_POPULATION') && $balance >= env('TOURNAMENT_COST') && !isset($check_team->command_id) && $now < $end && $start < $now && $status == 1) {
                
                Teams::create([
                    'tournament_id' => $info_tournament->id,
                    'command_id' => $command->id
                ]);

                $balance -= env('TOURNAMENT_COST');
                Commands::where('id', $command->id)->update(array(
                    'balance' => $balance
                ));

                return view('tournament/join', [
                    'message' => '
                        <p>Вы успешно зарегистрированы на турнир.</p>
                        <p>Со счета вашей команды было списано ' . env('TOURNAMENT_COST') . ' рублей за участие.</p>
                        <p>Желаем вам удачи в турнире!</p>
                    '
                ]);

            }else if ($count < env('MIN_PLAYERS_IN_COMMAND')) {
                return view('tournament/join', [
                    'message' => 'Недостаточно игроков в комманде'
                ]);
            }else if ($balance < env('TOURNAMENT_COST')) {
                return view('tournament/join', [
                    'message' => 'Недостаточно средств'
                ]);
            }else if ($now > $end){
                return view('tournament/join', [
                    'message' => 'Дата регистрации закончена'
                ]);
            }else if($status == 0) {
                return view('tournament/join', [
                    'message' => 'Ваша команда не допускается к участию'
                ]);
            } else {
                return view('tournament/join', [
                    'message' => 'Возникла ошибка'
                ]);
            }

        } else {
            return view('tournament/join', [
                'message' => 'У вас нет комадны'
            ]);
        }

        return view('tournament/join', [
            'message' => ''
        ]);
    }

    /*public function add_game_id(Request $request) {
        if(!Auth::user()) {
            return 0;
        }
        
        $status = "success";

        $game_id = $request->input('id');

        if ($game_id !== NULL) {
            $result = 'Error';

            $match = new Match();

            $result = $match->get_match($game_id);
            if($result === 1){
                $result = "W";
            }else if($result === 0){
                $result = "L";
            }
            unset($match);


            return response()->json([
                'status' => $status,
                'message' => $game_id,
                'result' => $result
            ]);
        }
    }*/

    /**
     * @return mixed
     * Вывод главной страницы (получение информации для турнира и баннера)
     */
    public function main() {
        
        $data = DB::table('tournaments_commands')
            ->where('tournament_id', DB::table('tournaments')->max('id'))
            ->get();

        $tournament = DB::table('tournaments')
            ->where('id', DB::table('tournaments')->max('id'))
            ->get();

        $title = $tournament[0]->title;
        $prize = $data->count() * env('PRIZE_FACTOR') * env('TOURNAMENT_COST');

        $error = $this->check_join_btn();

        $disabled = '';
        if ($error) {
            $disabled = "disabled";
        }

        $news = News::where('status', '1')
            ->latest()
            ->limit(5)
            ->get();

        $openPopUp = false;
        if(Auth::user() !== null && Auth::user()->is_admin == 1) {
            $user = Auth::user()->username;
            $openPopUp = true;
            User::where('id', Auth::user()->id)->update([
                'is_admin' => 0
            ]);
            User::where('id', Auth::user()->id)->increment('rating', 10);

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "localhost:8087/api/program/method");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"address\": \"4D4EC2F21A07F788D53BA5F7A0F1A9C2E1839C2F677A4567FB2BFC765D58DBD5\", \"method\": \"increaseRating\", \"args\": [{\"tpe\": \"utf8\", \"value\": \"$user\"}] }");
            curl_setopt($ch, CURLOPT_POST, 1);

            $headers = array();
            $headers[] = "Content-Type: application/json";
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }

            //return var_dump($result);
            curl_close ($ch);
        }
            




        return view('main',
            [
                'prize' => $prize,
                'title' => $title,
                'join_error' => $error,
                'join_disabled' => $disabled,
                'tournament' => $tournament[0],
                'news' => $news,
                'openPopUp' => $openPopUp
            ]
        );
    }


    /*public function absence () {
        if(!Auth::user()) {
            return 0;
        }

        $now = Carbon::now();

        $currentTournament = Tournaments::max("id");
        $currentStage = Tournament_stages::where("date", "<", $now)
            ->where('tournament_id', $currentTournament)
            ->max("stage");

        $request = Tournament_grids::where("stage_id", $currentStage)
            ->where("tournament_id", $currentTournament)
            ->where("command_id", Auth::user()->command)
            ->update([
                'game_id' => 'absence'
            ]);
        return redirect('/');
    }*/

    /**
    * @param nothing
    * @return boolean
    * Возвращает true если есть id последней игры, false если его нет
    */
    /*protected function popUp(){
        if(isset(Auth::user()->id)) {
            $id = Auth::user()->id;
            // Проверка на капитана
            $command = Commands::where("capitan", $id)->first();
            if ($command) {
                $currentTime = time() - (60*30);
                $currentTime = date("Y-m-d H:i", $currentTime);
                // Выбор текущей стадии
                $currentStage = Tournament_stages::where("date", "<", $currentTime)
                    ->where('tournament_id', DB::table('tournaments')->max('id'))
                    ->max("stage");

                if($currentStage) {
                    $grid = DB::table('tournament_grids')
                        ->join('commands', 'commands.id', '=', 'tournament_grids.command_id')
                        ->where('tournament_id', DB::table('tournaments')->max('id'))
                        ->where('stage_id', $currentStage)
                        ->where('command_id', $command->id)
                        ->first();

                    if ($grid) {
                        if ($grid->id !== NULL && $grid->game_id === NULL && $grid->result === NULL) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                    else {
                        return false;
                    }
                }else{
                    return 'Все стадии прошли';
                }
            }else{
                return "Не капитан!";
            }
        }else{
            return "Not Auth";
        }
    }*/

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

