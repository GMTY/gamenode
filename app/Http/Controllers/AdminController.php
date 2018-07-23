<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use App\User;
use App\Commands as Commands;
use App\Tournaments as Tournaments;
use App\Tournament_stages as Stage;
use App\Tournaments_commands as Teams;
use App\Tournament_grids as Grid;
use App\Http\Controllers\PaymentsController as PaymentsController;

use Validator;
use DB;

use Carbon\Carbon;

class AdminController extends Controller
{

    /**
     * Получаем информацию о текущем турнире
     * Используется повсеместно
     */
    public function __construct()
    {
        $this->payments = new PaymentsController();
        $this->tournamentId = DB::table('tournaments')
            ->max('id');

        $this->stageId = DB::table('tournament_grids')
            ->where('tournament_id', $this->tournamentId)
            ->max('stage_id');

        $this->maxStageId = DB::table('tournament_stages')
            ->where('tournament_id', $this->tournamentId)
            ->max('stage');
    }

    public function index() {
    	return view('admin.index');
    }

    public function news() {
        return view('admin.news');
    }

    /**
     * @return mixed
     * Вывод страницы турниров для администрирования
     */
    public function tournaments(){
        $tournaments = DB::table('tournaments')
            ->orderBy('id', 'DESC')
            ->paginate(env('TOURNAMENTS_LIMIT'));

        return view('admin.tournament.index',
            [
                'tournaments' => $tournaments
            ]
        );
    }

    /**
     * @return mixed
     * Вывод списка пользователей для администрирования
     */
    public function profiles(){
    	return view('admin.index');
    }

    /**
     * @return mixed
     * Вывод списка команд для администрирования
     */
    public function commands() {

        $id = DB::table('tournaments')->max('id');

        /**
         * Если сетка уже построена, значит регистрация на турнир закончилась
         */
        $isRegToTournamentEnded = DB::table('tournament_grids')
            ->where('tournament_id', $id)
            ->get();

        $isRegToTournamentEnded = count($isRegToTournamentEnded) ? true : false;

        $game = "bo1";

        if ($this->maxStageId - $this->stageId == 1) {
            $game = "bo3";
        }
        if ($this->maxStageId - $this->stageId == 0) {
            $game = "bo5";
        }

        /**
         * Если регистрация на турнир закончилась, то получаем комманды с данными
         * для дальнейшего функционала управления победами
         */
        if ($isRegToTournamentEnded) {

            $commands = DB::table('commands')
                ->leftJoin('tournament_grids', function ($join) {
                    $join->on('commands.id', '=', 'tournament_grids.command_id')

                        ->where('tournament_grids.tournament_id', '=', DB::table('tournaments')->max('id'))
                        ->where('tournament_grids.stage_id', '=',
                            DB::table('tournament_grids')
                                ->where('tournament_id', DB::table('tournaments')->max('id'))
                                ->max('stage_id')
                        );

                })
                ->orderBy('tournament_grids.tournament_id', 'DESC')
                ->select('commands.*', 'tournament_grids.stage_id','commands.id as command_id_number', 'tournament_grids.*', 'tournament_grids.id as grids_id')
                ->paginate(env('COMMANDS_ADMIN_LIMIT'));

        }

        else {

            /**
             * Если регистрация на турнир НЕ закончилась, смотрим, кто зарегистрировался на турнир
             * В зависимости от регистраций активируем кнопку "продвинуть в турнир"
             */
            $commands = DB::table('commands')
                ->leftJoin('tournaments_commands', function ($join) {
                    $join->on('commands.id', '=', 'tournaments_commands.command_id')

                        ->where('tournaments_commands.tournament_id', '=', DB::table('tournaments')->max('id'));

                })
                ->orderBy('commands.id', 'DESC')
                ->select('commands.*', 'commands.id as command_id', 'commands.id as command_id_number', 'tournaments_commands.command_id as registered', 'tournaments_commands.tournament_id as tournament_id')
                ->paginate(env('COMMANDS_ADMIN_LIMIT'));

            for ($i = 0; $i < count($commands); $i++) {
                if ($commands[$i]->registered) {
                    $commands[$i]->registered = true;
                }
                else {
                    $commands[$i]->registered = false;
                }

            }
        }

        return view('admin.commands', [
            'data' => $commands,
            'isRegToTournamentEnded' => $isRegToTournamentEnded,
            'game' => $game
        ]);
    }

    /**
     * @param int $id - id турнира
     * @return mixed
     * Вывод турнира для редактирования
     */
    public function tournament_edit($id = 0){

        if (!$id) {
            $id = DB::table('tournaments')->max('id');
        }

        $tournament = DB::table('tournaments')
            ->where('id', $id)
            ->get();

        $current_stage = DB::table('tournament_grids')
            ->where('tournament_id', $tournament[0]->id)
            ->max('stage_id');

        $commands = Grid::where('stage_id', $current_stage)
            ->where('tournament_id', $tournament[0]->id)
            ->orderBy('order','ASC')
            ->get();

        $stages = DB::table('tournament_stages')
            ->where('tournament_id', $tournament[0]->id)
            ->get();

        $status = '';
        if (count($commands) <= 2 && count($stages)) {
            $status = 'Турнир на финальной стадии';
        }

        if (!count($tournament)) {
            return redirect('admin/')->with('message', 'Турнир не существует');
        }

        return view('admin.tournament.edit',
            [
                'tournament' => $tournament[0],
                'status' => $status,
                'stages' => $stages
            ]
        );
    }

    /**
     * @param int $id
     * @return mixed
     * Вывод последнего турнира для редактирования
     */
    public function tournament_new(){
        return view('admin.tournament.new');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * Создаем новый турнир
     */
    public function tournament_create(Request $request){
    	$data = $request->all();

        $message = '';

        if ($data['title'] && $data['start'] && $data['end']) {

            if (strlen($data['title']) > env('TOURNAMENT_TITLE_MAX_LENGTH')) {
                $message = 'Слишком длинное название турнира';
            }

            if (!$message) {
                Tournaments::create([
                    'title' => $data['title'],
                    'content' => '',
                    'start' => $data['start'],
                    'end' => $data['end'],
                ]);
                $message = 'Турнир создан';
                return redirect('admin/tournaments')->with('message', $message);
            }

        }
        else {
            $message = "Не все поля заполнены!";
            return redirect('admin/tournament/new')->with('message', $message);
        }

        return redirect('admin/tournaments')->with('message', $message);
    }

    /**
     * @param int $id - ID команды для добавления в список регистраций на турнир
     * @return \Illuminate\Http\RedirectResponse
     */
    public function tournament_add_command($id = 0) {

        if ($id) {

            $tournament_id = DB::table('tournaments')->max('id');

            $isTournamentStarted = DB::table('tournament_grids')
                ->where('tournament_id', $tournament_id)
                ->get();

            /**
             * Добавление команды в турнир возможно лишь, если турнир не начался
             */
            if (!count($isTournamentStarted)) {

                $isCommandInRegisterList = DB::table('tournaments_commands')
                    ->where('tournament_id', $tournament_id)
                    ->where('command_id', $id)
                    ->get();

                /**
                 * Если команды нет в списке зарегистрированных, то добавляем
                 */
                if (!count($isCommandInRegisterList)) {

                    DB::table('tournaments_commands')
                        ->insert([
                            'tournament_id' => $tournament_id,
                            'command_id' => $id
                        ]);

                    return redirect('admin/commands/')->with('message', 'Команда зарегистрирована на турнир');
                }

            }

        }

        return redirect('admin/commands/')->with('message', 'При регистрации на турнир возникла проблема');
    }

    /**
     * @param Request $request
     * @return redirect
     * Изменяем данные турнира
     */
    public function tournament_save(Request $request){
        $data = $request->all();
        $id = $data['id'];

        $message = '';

        if ($data['title'] && $data['start'] && $data['end']) {

            if (strlen($data['title']) > env('TOURNAMENT_TITLE_MAX_LENGTH')) {
                $message = 'Слишком длинное название турнира';
            }

            if (!$message) {
                $isTour = DB::table('tournaments')
                    ->where('id', $id)
                    ->get();

                if ($isTour) {
                    DB::table('tournaments')
                        ->where('id', $id)
                        ->update(
                            [
                                'title' => $data['title'],
                                'content' => '',
                                'start' => $data['start'],
                                'end' => $data['end']
                            ]);

                    $message = 'Турнир обновлен';
                }
                else {
                    $message = 'Такой турнир не существует';
                }
            }

        }
        else {
            $message = "Не все поля заполнены!";
        }

        return redirect('admin/tournament/' . $id)->with('message', $message);
    }



    /**
     * @param Request $request
     * @return redirect
     * Изменяем информацию о этапах турнира
     */
    public function tournament_save_stages(Request $request){
        $data = $request->all();
        $id = $data['id'];

        $message = '';

        if (isset($data['title'])) {
            for ($i = 1; $i <= count($data['title']); $i++) {

                $title = isset($data['title'][$i]) ? $data['title'][$i] : '';
                $date = isset($data['date'][$i]) ? $data['date'][$i] : '';

                if (strlen($title) < env('MAX_STAGE_TITLE_LENGTH')) {

                    if ($title && $date) {

                        DB::table('tournament_stages')
                            ->where('tournament_id', $id)
                            ->where('stage', $i)
                            ->update([
                                'title' => $data['title'][$i],
                                'date' => $data['date'][$i]
                            ]);

                    }

                }

            }
        }

        $message = 'Информация об этапах турнира обновлена';

        return redirect('admin/tournament/' . $id)->with('message', $message);
    }


    /**
     * @return string
     * Создает первоначальную сетку турнира и высчитывает количество этапов турнира (заносит в БД)
     * TODO: возможно стоит добавить номер этапа, чтобы использовать функцию на следующих этапах
     */
    public function make_grid($tournament_id = 0, $stage = 1) {
        $i = 0;

        if (!$tournament_id) {
            $tournament_id = DB::table('tournaments')->max('id');
        }

        $grid = Teams::where('tournament_id', $tournament_id)->get();
        while(isset($grid[$i]->command_id)){
            $commands_id[$i] = $grid[$i]->command_id;
            $i++;
        }

        // Проверка на колчество команд
        if ($grid->count() > 1) {
            shuffle($commands_id);  
        }
        
        else{
            return "Недостаточно зарегестрированных команд на данный турнир!";
        }
        

        $count_stages = $this->count_stages($commands_id);

        $stages = DB::table('tournament_stages')
            ->where('tournament_id', $tournament_id)
            ->get();

        if (!count($stages)) {

            for ($j = 1; $j <= $count_stages; $j++) {
                $now = mktime(date('H') + 3, 0, 0, date("m"), date("d") + 7 * $j, date("Y"));
                $date = date("Y-m-d H:i", $now);

                DB::table('tournament_stages')
                    ->insert([
                        'tournament_id' => $tournament_id,
                        'stage' => $j,
                        'title' => $j . ' этап',
                        'date' => $date
                    ]);
            }
        }

        $isGridExist = DB::table('tournament_grids')
            ->where('tournament_id', $tournament_id)
            ->get();

        if (!count($isGridExist)) {

            for ($j = 0; $j < $i; $j++) {

                $tour_gr = new Grid;
                $tour_gr->command_id = $commands_id[$j];
                $tour_gr->tournament_id = $tournament_id;
                $tour_gr->stage_id = $stage;
                $tour_gr->order = $j;

                // если последняя команда без соперника, то автоматом побеждает в этапе
                if (($j == $i - 1) && $this->check_parity($j)) {
                    $tour_gr->result = true;
                }

                $tour_gr->save();
                unset($tour_gr);

            }
        }

        return "Сетка создана!";
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

    /**
     * @param $arr
     * @return int
     * Рассчитываем до какого количества нужно заполнить массив, чтобы была степень 2-ки
     * TODO: Идиотизм, степень двойки, рефакторить!
     */
    private function count_stages($arr) {
        $count = count($arr);

        if ($count <= 2) {
            return 1;
        }
        if ($count > 2 and $count <= 4 ) {
            return 2;
        }
        if ($count > 4 and $count <= 8 ) {
            return 3;
        }
        if ($count > 8 and $count <= 16 ) {
            return 4;
        }
        if ($count > 16 and $count <= 32 ) {
            return 5;
        }
        if ($count > 32 and $count <= 64 ) {
            return 6;
        }
        if ($count > 64 and $count <= 128 ) {
            return 7;
        }

        return 0;
    }


    /**
     * @return view admin.commands
     * Блокируем комманду (даём бан) - меня в таблице комманд статус на 0
     */

    public function ban(Request $request) {
        $id = $request->input('id');
        Commands::where('id', $id)->update([
            'status' => 0
        ]);
        return back();
    }

    /**
     * @return view admin.commands
     * Разблокируем комманду (снимаем бан) - меня в таблице команд статус на 1
     */

    public function unban(Request $request) {
        $id = $request->input('id');
        Commands::where('id', $id)->update([
            'status' => 1
        ]);
        return back();
    }

    /**
     * @param Request data - id в таблице tournament_grids
     * @return Возвращает обратно на страницу управления комманд
     * Смотрит id, проверяет порядок на чётность,
     * взависимости от результата присваевает выигрышь команде, и проигрышь опонентам
    */
    public function promote(Request $request) {

        $id = $request->input('id');
        $commandId = $request->input('commandId');

        $commandOpponent = DB::table('tournament_grids')
            ->where('tournament_id', $this->tournamentId)
            ->where('stage_id', $this->stageId)
            ->where('command_id', '!=', $commandId)
            ->first();

        // добавляем к выплатам проигравшего
        $this->payments->addToWaitPayments($commandOpponent->command_id, $this->tournamentId, $this->stageId);

        // для финалистов обе команды добавляем в таблицу выплат
        if ($this->maxStageId - $this->stageId == 0) { // финал
            $this->payments->addToWaitPayments($commandId, $this->tournamentId, $this->stageId + 1);
        }

        $checkParity = Grid::where('id', $id)->first()->order;
        $checkParity = $this->check_parity($checkParity);

        if($checkParity) {
            Grid::where('id', $id)->update([
                'result' => 1
            ]);

            Grid::where('id', ($id + 1))->update([
                'result' => 0
            ]);

        }
        else {

            Grid::where('id', $id)->update([
                'result' => 1
            ]);

            Grid::where('id', ($id - 1))->update([
                'result' => 0
            ]);

        }

        return back();
    }


    /**
    * @param $pass - пароль для доступа использования данной функции
    * @return string состояние о турире.
    * Создаёт первоначальную сетку, если таковой нет
    * если, она существует делает проверку на дальнешее количество участников, если участников достаточно создаёт следующий этап
    * если, участников 2-е то заканчивает турнир и начисляет участникам рейтинг
    */
    public function nextStageCron($pass){


        if($pass == env('CRON_TOURNAMENTS_NEXT_STAGE_PASSWORD')){

            $tournament = DB::table('tournaments')
                ->where('id', DB::table('tournaments')->max('id'))
                ->first();

            if($tournament->end > Carbon::now()) {
                return 'Ещё не завершилась регистрация на турнир';
            }

            $tournament_id = $tournament->id;

            $checkFirstGrid = DB::table('tournament_stages')
                ->where('tournament_id',$tournament_id)
                ->where('stage', '=', 1) // TODO: по-моему это лишнее условие
                ->first();

            // Создание первоначальной сетки
            if($checkFirstGrid === NULL){
                return $this->make_grid();
            }

            $current_stage = DB::table('tournament_grids')
                ->where('tournament_id', $tournament_id)
                ->max('stage_id');

            $possibleNewStage = DB::table('tournament_stages')
                ->where('stage', ($current_stage + 1))
                ->where('tournament_id', $tournament_id)
                ->where('date', '<', Carbon::now())
                ->first();

            $maxIdStage = DB::table('tournament_stages')
                ->where('tournament_id', $tournament_id)
                ->max('stage');

            // Если время для следующей стадии не наступило
            if($possibleNewStage === NULL && ($current_stage + 1) <= $maxIdStage) {
                return 'Время стадии ещё не наступило';
            }

            if($tournament->status == 1){
                return 'Турнир закончился, нельзя создать новый этап';
            }

            // Начисление рейтинга
            //$this->rating($tournament_id); // TODO почему это здесь?

            /**
             * Если у нас две соревнующиеся команды не имеют результата, то
             * случайным образом побеждает одна из 2-х
             * TODO: выбывают обе команды, если у них absense (result = 2) / Переделать логику заполнения таблицы (фронт построение сетки)
             */
            $commands = Grid::where('stage_id', $current_stage)
                ->where('tournament_id', $tournament_id)
                ->orderBy('order','ASC')
                ->get();

            if (count($commands) > 2) { // если эта не финальный, то есть команд больше 2-х, то анализируем полноту результатов
                for ($i = 0; $i < count($commands); $i++) {

                    // проходим только по четным order и анализируем нечетные сразу за ними
                    if ($commands[$i]->order % 2 == 0 && ($i + 1) < count($commands)) {
                        if ($commands[$i]->result === NULL && $commands[$i + 1]->result === NULL) {
                            $result = rand(0, 1);
                            $result2 = $result ? 0 : 1;

                            Grid::where('id', $commands[$i]->id)
                                ->update([
                                    'result' => $result
                                ]);

                            Grid::where('id', $commands[$i + 1]->id)
                                ->update([
                                    'result' => $result2
                                ]);
                        }
                    }

                }
            }

            $commandsInfo = DB::table('tournament_grids')
                ->where('tournament_id', $tournament_id)
                ->where('stage_id', $current_stage)
                ->orderBy('order', 'ASC')
                ->get();

            $commands = [];
            $order = 0;
            for ($i = 0; $i < count($commandsInfo); $i++) {
                // проходим по всем записям в grids
                if ($commandsInfo[$i]->order % 2 == 0 && ($i + 1) < count($commandsInfo)) {
                    if ($commandsInfo[$i]->result > $commandsInfo[$i + 1]->result) {
                        $commands[$order]['tournament_id'] = $tournament_id;
                        $commands[$order]['stage_id'] = $current_stage + 1;
                        $commands[$order]['command_id'] = $commandsInfo[$i]->command_id;
                    }
                    else {
                        $commands[$order]['tournament_id'] = $tournament_id;
                        $commands[$order]['stage_id'] = $current_stage + 1;
                        $commands[$order]['command_id'] = $commandsInfo[$i + 1]->command_id;
                    }
                    $commands[$order]['order'] = $order;
                    $commands[$order]['result'] = NULL;
                    $order++;
                }
                else {
                    // крайняя команда без соперника попадает в следующий этап тоже.
                    // Пример, играют пары команд: 1-2, 3-4, 5 (6й нет)
                    if ($commandsInfo[$i]->order % 2 == 0 && ($i + 1) >= count($commandsInfo)) {
                        $commands[$order]['tournament_id'] = $tournament_id;
                        $commands[$order]['stage_id'] = $current_stage + 1;
                        $commands[$order]['command_id'] = $commandsInfo[$i]->command_id;
                        $commands[$order]['order'] = $order;
                        $commands[$order]['result'] = NULL;
                    }
                }
            }

            if (count($commands) % 2 == 1) {
                // крайняя команде без соперника попадает в следующий этап тоже.
                // Пример, играют пары команд: 1-2, 3-4, 5 (6й нет)
                $commands[count($commands) - 1]['result'] = 1;
            }
            
            if (count($commands) >= 2) {

                DB::table('tournament_grids')
                    ->insert($commands);

                return "Стадия создана!";
            }

            /*else if (count($commands) == 0){
                return "Нет результатов!";
            }*/

            else if (count($commands) <= 2 && count($commands) != 0) { // конец турнира
                Tournaments::where('id', $tournament_id)
                ->update([
                    'status' => 1
                ]);
                return "Турнир окончен";
            }


            else {
                return "Финал турнира уже наступил. Больше этапов у данного турнира не может быть";
            }

        }

        return "Не введён пароль!";
    }


    /**
     * @param $tournament_id - id незавершённого текущего турнира
     * @return void
     * Начисляет рейтинг взависимости от стадии турнира команде и игрокам
     *
     * TODO вынести всю логику в отдельный контроллер по управлению рейтингом команд и игроков
    */
    private function rating($tournament_id) {

        $current_stage = DB::table('tournament_grids')
            ->where('tournament_id', $tournament_id)
            ->max('stage_id');

        $currentStageNumber = DB::table('tournament_stages')
            ->where('id', $current_stage)
            ->first()
            ->stage;

        $ratingWin = env('TOURNAMENTS_WIN_RATING') * $currentStageNumber;
        $ratingLose = env('TOURNAMENTS_LOSE_RATING') * $currentStageNumber;

        $winTeam = DB::table('tournament_grids')
            ->where('tournament_grids.tournament_id', '=', DB::table('tournaments')->max('id'))
            ->where('tournament_grids.stage_id', '=', $current_stage)
            ->where('tournament_grids.result', '=', 1)->get();

        for ($j=0; $j < $winTeam->count(); $j++) {
            User::where('command', $winTeam[$j]->command_id)->increment('rating', $ratingWin);
            Commands::where('id', $winTeam[$j]->command_id)->increment('rating', $ratingWin);     
        }

        $loseTeam = DB::table('tournament_grids')
            ->where('tournament_grids.tournament_id', '=', DB::table('tournaments')->max('id'))
            ->where('tournament_grids.stage_id', '=', $current_stage)
            ->where('tournament_grids.result', '=', 0)->get();

        for ($j=0; $j < $loseTeam->count(); $j++) {
            User::where('command', $loseTeam[$j]->command_id)->increment('rating', $ratingLose);
            Commands::where('id', $loseTeam[$j]->command_id)->increment('rating', $ratingLose);     
        }
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     * Устанавливаем баланс команде
     */
    public function balance(Request $request) {
        $balance = $request->input('balance');
        if ($balance === NULL){
            return back();
        }

        $validator = Validator::make($request->all(), [
            'balance' => 'numeric|min:0'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors("Баланс должен быть положительным числом.")
                ->withInput();
        }

        $commandId = $request->input('commandId');

        Commands::where('id', $commandId)
            ->update([
                'balance' => $balance
            ]);

        return back()->with('message', 'Баланс команды установлен: ' . $balance . ' рублей');
    }

    /**
     * Получение списка команд, ожидающих выплаты
     */
    public function payments()
    {
        $payments = DB::table('tournaments_payments')
            ->where('is_paid', 0)
            ->orderBy('id', 'DESC')

            ->join('commands', 'commands.id', '=', 'tournaments_payments.command_id')
            ->join('tournaments', 'tournaments.id', '=', 'tournaments_payments.tournament_id')
            ->select(
                'tournaments_payments.id as id',
                'commands.id as command_id',
                'commands.name',
                'tournaments_payments.is_paid',
                'tournaments_payments.stage_id',
                'tournaments.title',
                'tournaments.id as tournament_id')

            ->orderBy('tournaments_payments.id', 'DESC')
            ->paginate(env('PAYMENTS_ADMIN_LIMIT'));


        return view('admin.payments',
            [
                'payments' => $payments,
                'type' => 'wait'
            ]
        );
    }

    /**
     * Получение списка команд, которым выплатили деньги
     */
    public function listPaymentsDone()
    {
        $payments = DB::table('tournaments_payments')
            ->where('is_paid', 1)
            ->orderBy('id', 'DESC')

            ->join('commands', 'commands.id', '=', 'tournaments_payments.command_id')
            ->join('tournaments', 'tournaments.id', '=', 'tournaments_payments.tournament_id')
            ->select(
                'tournaments_payments.id as id',
                'commands.id as command_id',
                'commands.name',
                'tournaments_payments.is_paid',
                'tournaments_payments.stage_id',
                'tournaments.title',
                'tournaments.id as tournament_id')

            ->orderBy('tournaments_payments.id', 'DESC')
            ->paginate(env('PAYMENTS_ADMIN_LIMIT'));


        return view('admin.payments',
            [
                'payments' => $payments,
                'type' => 'done'
            ]
        );
    }

    /**
     * Обновление QIWI-кошелька команды (изменить QIWI кошелек может только админ)
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeQiwi(Request $request)
    {
        $data = $request->all();

        $updateQiwi = DB::table('commands')
            ->where('id', $data['id'])
            ->update([
                'qiwi' => $data['qiwi']
            ]);

        if ($updateQiwi) {
            return redirect('admin/commands')->with('message', 'Qiwi кошелек изменен');
        }
        else {
            return redirect('admin/commands')->with('message', 'Qiwi кошелек НЕ изменен');
        }

    }
}
