<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\PaymentsController as PaymentsController;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApiController extends Controller
{
    private $tournamentId;
    private $stageId;
    private $maxStageId;
    private $payments;

    /**
     * Получаем информацию о текущем турнире
     * Используется повсеместно
     */
    function __construct()
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

    /**
     * @param Request $request
     * Тестовая функция для приема JSON объекта для бота
     */
    public function getRequestCreateLobby(Request $request)
    {
        dump($request->all());
    }

    /**
     * Запрос к боту для создания лобби для участников турнира
     * Сначала проверяем, отправлены ли запросы на ботов и все ли боты запущены
     *
     * В запрос к боту нужно передать структуру вида
     *  {
     *      lobby: [
     *          'lobby_id' : ...,
     *          'team' : [
     *              { name: ..., members: [1-6]},
     *              { name: ..., members: [1-6]}
     *          ],
     *          'team' : [
     *              { name: ..., members: [1-6]},
     *              { name: ..., members: [1-6]}
     *          ]
     *      ],
     *      ...
     *  }
     */
    public function createLobby(){

        $botsExist = DB::table('bots')
            ->where('tournament_id', $this->tournamentId)
            ->where('stage_id', $this->stageId)
            ->where('type', 'createRequest')
            ->where('status', 'sent')
            ->count();

        if (!$botsExist) {
            return "Bots aren't created for stage";
        }

        $botsLogined = DB::table('bots')
            ->where('tournament_id', $this->tournamentId)
            ->where('stage_id', $this->stageId)
            ->where('type', 'steamLogin')
            ->where('status', 'ok')
            ->count();

        if ($botsLogined != $botsExist) {
            return "Not all bots are logined";
        }

        $stage = DB::table('tournament_stages')
            ->where('tournament_id', $this->tournamentId)
            ->where('stage', $this->stageId)
            ->first();

        if (strtotime($stage->date) - time() > 0) {
            return "The time of the stage has not yet come";
        }

        $maxStageId = DB::table('tournament_stages')
            ->where('tournament_id', $this->tournamentId)
            ->max('stage');

 		$obj = (object)[];

        if($this->stageId !== NULL) {

            //game type
            $gameType = "bo1";
            if ($maxStageId - $this->stageId == 0) {
                $gameType = "bo5"; // финал
            }
            if ($maxStageId - $this->stageId == 1) {
                $gameType = "bo3"; // полуфинал
            }

            $countTeams = DB::table('tournament_grids')
                ->where('tournament_id', $this->tournamentId)
                ->where('stage_id', $this->stageId)
                ->count();

            $data = DB::table('tournament_grids')
                ->where('tournament_id', $this->tournamentId)
                ->where('stage_id', $this->stageId)
                ->join('commands', 'commands.id', '=', 'tournament_grids.command_id')
                ->join('users', 'users.command', '=', 'tournament_grids.command_id')
                ->select('users.steamid', 'commands.name', 'tournament_grids.order', 'commands.id', 'tournament_grids.lobby_id')
                ->orderBy('tournament_grids.order')
                ->get();

            if ($data[0]->lobby_id > 0) {
                return 'Request for lobby already submitted';
            }

            $playersCount = $data->count();
            $counter = 0;

            $obj->count = intval(floor($countTeams / 2)); // кол-во лобби

            $maxLobbyId = DB::table('games_status')
                ->max('id') + 1;

            $lobbies_reserved = [];

            for ($i = 0; $i < $obj->count; $i++) {
                $lobbies_reserved[$i]['lobby_id'] = $maxLobbyId + $i;
                $lobbies_reserved[$i]['status'] = 'reserved';
            }
            // резервируем ID лобби для команд, заносим их в таблицу истории игр
            DB::table('games_status')->insert($lobbies_reserved);

            for ($lobby = 0; $lobby < $obj->count; $lobby++) {

                $obj->lobby[$lobby]['lobby_id'] = $lobbies_reserved[$lobby]['lobby_id'];
                $obj->lobby[$lobby]['series_type'] = $gameType;

                $spectators = [env('SPECTATORS_ID1'), env('SPECTATORS_ID2')];
                $obj->lobby[$lobby]['spectators'] = $spectators; // steamID for Admins

                for ($team = 0; $team <= 1; $team++) {

                    if($counter == $playersCount) {
                        break;
                    }

                    $obj->lobby[$lobby]['team'][$team]['name'] = $data[$counter]->name;
                    $obj->lobby[$lobby]['team'][$team]['id'] = $data[$counter]->id;
                    $oldOrder = $data[$counter]->order;

                    // проставляем ID лобби для каждой команды в сетке этапа турнира
                    DB::table('tournament_grids')
                        ->where('tournament_id', $this->tournamentId)
                        ->where('stage_id', $this->stageId)
                        ->where('command_id', $data[$counter]->id)
                        ->update([
                            'lobby_id' => $obj->lobby[$lobby]['lobby_id']
                        ]);

                    for ($i = 0; $i < env('MAX_PLAYERS_IN_COMMAND'); $i++) {

                        if($counter < $playersCount && $oldOrder == $data[$counter]->order) {
                            $obj->lobby[$lobby]['team'][$team]['members'][] = $data[$counter]->steamid;
                            $counter++;
                        }

                    }
                }
            }

            // тестовый steamID
            //$obj->lobby[0]['team'][1]['members'][0] = '76561198063914574';
            //$obj->lobby[0]['team'][0]['members'][0] = '76561198172398818';

            dump($obj);

		    $data_string = json_encode($obj);

            $curl = curl_init();
    
            //curl_setopt($curl, CURLOPT_URL, env('APP_URL').'/api/getRequestCreateLobby');
            curl_setopt($curl, CURLOPT_URL, env('BOT_SERVER') . '/createLobby');
            curl_setopt($curl, CURLOPT_POST, 1);
		    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
		    curl_setopt($curl, CURLOPT_HTTPHEADER,
                array (
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_string)
                )
		    );
    
		    curl_exec($curl);
		    curl_close($curl);
        }
	}


    /**
     * Запрос на создание ботов
     * В POST нужно указать количество ботов: count = X (1-64)
     * Запрос отправляется за 5 минут до начала очередной стадии турнира, чтобы боты успели запуститься и залогиниться
     */
    public function createBot(){

        $botExist = DB::table('bots')
            ->where('tournament_id', $this->tournamentId)
            ->where('stage_id', $this->stageId)
            ->where('type', '!=', 'destroy')
            ->count();

        if ($botExist) {
            return "Bots exist for this stage";
        }

        // считаем, сколько будет команд и сколько ботов нам нужно
        $countTeams = DB::table('tournament_grids')
            ->where('tournament_id', $this->tournamentId)
            ->where('stage_id', $this->stageId)
            ->where('result', NULL)
            ->count();

        $count = floor($countTeams / 2); // кол-во лобби

        for ($i = 1; $i <= $count; $i++) {
            $bots_info[$i]['tournament_id'] = $this->tournamentId;
            $bots_info[$i]['stage_id'] = $this->stageId;
            $bots_info[$i]['bot_id'] = $i;
            $bots_info[$i]['status'] = 'sent';
            $bots_info[$i]['type'] = 'createRequest';
        }

        // создаем запрос на включение ботов
        if (isset($bots_info)) {
            DB::table('bots')->insert($bots_info);

            /**
             * Запускаем ботов
             */
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, env('BOT_SERVER') . '/start');
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, "count=".$count);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER,
                array (
                    'Content-Type: application/x-www-form-urlencoded'
                )
            );

            curl_exec($curl);
            $info = curl_getinfo($curl);

            if ($info['http_code'] == 200) {
                echo "Bots request sent successfully";
            }
            else {
                echo "Error while bot's creation. Error code: " . $info['http_code'];
            }

            curl_close($curl);
        }
        else {
            echo "Bots are not need for stage";
        }

    }

    /**
     * POST Ответ от бота (NODE JS, env('BOT_SERVER'))
     *
     * $request содержит:
     * id - id бота
     * lobby_id - id лобби
     * type - тип ответа
     * response - содержание ответа
     */
    public function botResponse(Request $request)
    {
        $responseBot = $request->all();

        $id = $responseBot['id'];
        $lobbyId = isset($responseBot['lobby_id']) ? $responseBot['lobby_id'] : 0;
        $response = $responseBot['response'];
        $type = $responseBot['type'];

        switch ($type) {

            /**
             * Ответ: Авторизация бота
             * response: ok или код ошибки (5 - неверный логни\пароль, 63 - SteamGuard включен)
             */
            case 'steamLogin':
                // вставить запись в bots
                DB::table('bots')->insert([
                    'tournament_id' => $this->tournamentId,
                    'stage_id' => $this->stageId,
                    'bot_id' => $id,
                    'status' => $response,
                    'type' => $type
                ]);
                break;

            /**
             * Ответ: Ситация с созданием лобби
             */
            case 'createLobby':
                DB::table('games_status')->insert([
                    'lobby_id' => $lobbyId,
                    'status' => $type
                ]);
                break;

            /**
             * Ответ: Игровые события
             * response: start, end, cancel, failed
             */
            case 'game':

                DB::table('games_status')->insert([
                    'lobby_id' => $lobbyId,
                    'status' => $response
                ]);

                // вставить запись в bots
                DB::table('bots')->insert([
                    'tournament_id' => $this->tournamentId,
                    'stage_id' => $this->stageId,
                    'bot_id' => $id,
                    'status' => $response,
                    'type' => $type
                ]);

                // обе команды не пришли на игру
                if ($response == "cancel") {
                    DB::table('tournament_grids')
                        ->where('tournament_id', $this->tournamentId)
                        ->where('stage_id', $this->stageId)
                        ->where('lobby_id', $lobbyId)
                        ->update([
                            'result' => env('VALUE_ABSENCE')
                        ]);


                    $commands = DB::table('tournament_grids')
                        ->where('tournament_id', $this->tournamentId)
                        ->where('stage_id', $this->stageId)
                        ->where('lobby_id', $lobbyId)
                        ->get();

                    foreach ($commands as $command) {
                        $this->payments->addToWaitPayments($command->command_id, $this->tournamentId, $this->stageId);
                    }

                }

                // бот не смог создать игру
                if ($response == "failed") {
                }

                break;

            /**
             * Ответ: Победа команды в лобби (bo1, bo3, bo5, алгоритм одинаковый)
             */
            case 'win':
            case 'win_bo3':
            case 'win_bo5':

                DB::table('games_status')->insert([
                    'lobby_id' => $lobbyId,
                    'status' => $type
                ]);

                // присваиваем победу команде
                $winner = DB::table('tournament_grids')
                    ->where('tournament_id', $this->tournamentId)
                    ->where('stage_id', $this->stageId)
                    ->where('lobby_id', $lobbyId)
                    ->where('command_id', $response)
                    ->first();

                if ($type == 'win') {
                    if ($winner->result !== NULL) {
                        $winner->result++;
                    } else {
                        $winner->result = 1;
                    }
                }

                DB::table('tournament_grids')
                    ->where('tournament_id', $this->tournamentId)
                    ->where('stage_id', $this->stageId)
                    ->where('lobby_id', $lobbyId)
                    ->where('command_id', $response)
                    ->update([
                        'result' => $winner->result
                    ]);

                // кто в итоге выбывает?
                $failedCommand = DB::table('tournament_grids')
                    ->where('tournament_id', $this->tournamentId)
                    ->where('stage_id', $this->stageId)
                    ->where('lobby_id', $lobbyId)
                    ->get();

                if ($failedCommand[0]->result > $failedCommand[1]->result) {
                    $failedCommandId = $failedCommand[1]->command_id;
                }
                else {
                    $failedCommandId = $failedCommand[0]->command_id;
                }

                // победа в bo1
                if ($type == 'win' && $this->maxStageId - $this->stageId > 2) { // не учитываем промежуточные победы в bo3|5
                    $this->payments->addToWaitPayments($failedCommandId, $this->tournamentId, $this->stageId);
                }

                // победа в bo3
                if ($type == 'win_bo3') { // итоговая победа в bo3|5
                    $this->payments->addToWaitPayments($failedCommandId, $this->tournamentId, $this->stageId);
                }

                // победа в bo5
                if ($type == 'win_bo5') { // итоговая победа в bo3|5
                    $this->payments->addToWaitPayments($failedCommandId, $this->tournamentId, $this->stageId);

                    // при финале турнира победителя тоже отправляем в таблицу для выплат
                    $this->payments->addToWaitPayments($response, $this->tournamentId, $this->stageId + 1);
                }

                break;

            /**
             * Ответ: техническое поражение
             * response: проигравшая команда
             */
            case 'techDef':
                DB::table('games_status')->insert([
                    'lobby_id' => $lobbyId,
                    'status' => $type
                ]);

                $this->payments->addToWaitPayments($response, $this->tournamentId, $this->stageId);

                DB::table('tournament_grids')
                    ->where('tournament_id', $this->tournamentId)
                    ->where('stage_id', $this->stageId)
                    ->where('lobby_id', $lobbyId)
                    ->where('command_id', $response)
                    ->update([
                        'result' => env('VALUE_ABSENCE')
                    ]);

                // идиотизм на скорую руку
                DB::table('tournament_grids')
                    ->where('tournament_id', $this->tournamentId)
                    ->where('stage_id', $this->stageId)
                    ->where('lobby_id', $lobbyId)
                    ->where('result', NULL)
                    ->update([
                        'result' => 1
                    ]);
                break;

            default:
                echo "Undefined response type";
                break;
        }

        dump($response);

    }


    /**
     * Тестовая функция для эмуляции ответа бота
     */
    public function testBotResponse()
    {
        $botResponse = (object)[];

        $botResponse->id = 1;
        $botResponse->response = '127';
        $botResponse->type = 'win_bo5';
        $botResponse->lobby_id = 555;

        $botResponse = json_encode($botResponse);

        dump($botResponse);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, env('APP_URL').'/api/botresponse');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $botResponse);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array (
                'Content-Type: application/json',
                'Content-Length: ' . strlen($botResponse)
            )
        );

        curl_exec($curl);
        $info = curl_getinfo($curl);

        if ($info['http_code'] == 200) {

            echo "Тест завершен";

        }
        else {

            echo "Возникла проблема при тестировании ботов. Error code: " . $info['http_code'];

        }

        curl_close($curl);
    }


    /**
     * GET-запрос на удаление всех ботов
     */
    public function botsdestroy()
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, env('BOT_SERVER') . '/destroyAll');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);

        curl_exec($curl);
        $info = curl_getinfo($curl);

        if ($info['http_code'] == 200) {
            echo "Bots are destroed";

            // делаем пометку, что отправили запрос на удаление ботов и они удалены
            DB::table('bots')->insert([
                'bot_id' => 0,
                'tournament_id' => $this->tournamentId,
                'stage_id' => $this->stageId,
                'status' => 'sent',
                'type' => 'destroy'
            ]);
        }
        else {
            echo "Error code: " . $info['http_code'];
        }

        curl_close($curl);

    }

    /**
     * CRON задача для работы с ботами и лобби
     *
     * Процесс:
     * Сначала отправляется запрос на уничтожение всех ботов. В базу заности запись.
     * Если удаление было выполнено, то идет запрос на создание ботов
     * После создания ботов (и успешного логина ботами) отправляется запрос на создание лобби
     */
    public function processTournament()
    {
        $itIsTimeForStage = DB::table('tournament_stages')
            ->where('stage', $this->stageId)
            ->where('tournament_id', $this->tournamentId)
            ->where('date', '<', Carbon::now())
            ->first();

        if (!$itIsTimeForStage) {
            return "Stage's time didn't come";
        }

        $isBotDestroyed = DB::table('bots')
            ->where('tournament_id', $this->tournamentId)
            ->where('stage_id', $this->stageId)
            ->where('type', 'destroy')
            ->first();

        if (!$isBotDestroyed) {
            print $this->botsdestroy();
        }
        else {
            print $this->createBot();
            print $this->createLobby();
        }
    }


}
