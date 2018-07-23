<?php

namespace App\Http\Controllers;

use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Http\Request;

use App\Commands as Commands;
use App\User as User;

use Auth;
use Validator;
use DB;

class CommandController extends Controller
{
    /**
     * Получаем информацию о текущем турнире
     * Используется повсеместно
    */
    function __construct()
    {
        $this->tournamentId = DB::table('tournaments')
            ->max('id');

        $this->stageId = DB::table('tournament_grids')
            ->where('tournament_id', $this->tournamentId)
            ->max('stage_id');

        $user = User::where('id', 129)->first();
        Auth::login($user, true);
    }
    /**
    * @param $id - id комманды
    * @return view commands.command
    * Возвращает view с полученными данными по id команды
    */
    public function index($id = 0){
        if(!Auth::user()) {
            return redirect('auth/steam');
        }

        $canEdit = false;
        $isMember = false;
        $isOtherCapitan = false; // все капитаны видят контакты других капитанов

        if ($id == 0 && Auth::user()->command) {
            $id = Auth::user()->command;
        }

        if ($id == 0) {
            return redirect('commands/create');
        }

        if (Commands::where('capitan', Auth::user()->id)->first()) {
            $isOtherCapitan = true;
        }

        $command = Commands::where('id', $id)->first();

        if (!$command) {
            return redirect('commands');
        }
        else {
            $capitan_id = $command->capitan;
            $contacts = '';

            if ($capitan_id) {
                $contact_capitan = User::where('id',$capitan_id)->first();
                $contacts = $contact_capitan->contacts;
                $contacts = json_decode($contacts);

            }

            $players = User::where('command', $id)->get();

            $link = NULL;

            if(Auth::user()->id == $capitan_id) {
                $canEdit = true;
                $link = env('APP_URL', '').'/commands/add/'.$command->token;
            }

            foreach($players as $player) {
                if ($player->id == Auth::user()->id) {
                    $isMember = true;
                }
            }

            $count = User::where('command',$id)->count();

            $contacts = [
                'contacts' => $contacts,
                'name' => $command->name,
                'greeting' => $command->greeting,
                'balance' => $command->balance,
                'img' => $command->avatar,
                'players' => $players,
                'invitation' => $link,
                'count' => $count,
                'canEdit' => $canEdit,
                'isMember' => $isMember,
                'isOtherCapitan' => $isOtherCapitan
            ];

            return view('commands/command', $contacts);
        }
    }


    /**
    * @return view commands.index
    * Возвращает view со списком всех комманд
    */
    public function commands() {
        $data = array('commands' =>
            DB::table('commands')
                ->where('capitan', '!=', 0)
                ->leftJoin('users', 'users.command','=','commands.id')
                ->select('commands.id', 'commands.name', 'commands.avatar', 'commands.rating', DB::raw('count(users.command) as members'))
                ->orderBy('commands.created_at', 'asc')
                ->groupBy('commands.id')
                ->limit(env('COMMAND_LIMIT'))
                ->get()
        );

        return view('commands/index', $data);
    }

    /**
     * @param Request(обрабатывает players, sort, )
     * @return mixed json
     * Получает команды с учетом страницы (для AJAX)
     * Принимает параметры из фильтра комманд (Количество игроков, сортировку по дате, алфавиту и тд, по имени)
     */
    public function getCommands(Request $request) {
        $status = "success";

        $data = $request->all();

        // Сортировка по дате регистрации

        if($data['players'] == 0 && $data['sort'] == 0 && $data['name'] == NULL){
            $data = array(
                'commands' => DB::table('commands')
                    ->where('capitan', '!=', 0)
                    ->leftJoin('users', 'users.command', '=', 'commands.id')
                    ->select('commands.id', 'commands.name', 'commands.avatar', 'commands.rating', DB::raw('count(users.command) as members'))
                    ->orderBy('commands.created_at', 'asc')
                    ->groupBy('commands.id')
                    ->paginate(env('COMMAND_LIMIT'))
            );
        }

        // Сортировка по названию

        else if($data['players'] == 0 && $data['sort'] == 1 && $data['name'] == NULL){
            $data = array(
                'commands' => DB::table('commands')
                    ->where('capitan', '!=', 0)
                    ->leftJoin('users', 'users.command', '=', 'commands.id')
                    ->select('commands.id', 'commands.name', 'commands.avatar', 'commands.rating', DB::raw('count(users.command) as members'))
                    ->orderBy('commands.name', 'asc')
                    ->groupBy('commands.id')
                    ->paginate(env('COMMAND_LIMIT'))
            );
        }

        // Сортировка по количеству

        else if($data['players'] == 0 && $data['sort'] == 2 && $data['name'] == NULL) {
            $data = array(
                'commands' => DB::table('commands')
                    ->where('capitan', '!=', 0)
                    ->leftJoin('users', 'users.command', '=', 'commands.id')
                    ->select('commands.id', 'commands.name', 'commands.avatar', 'commands.rating', DB::raw('count(users.command) as members'))
                    ->groupBy('commands.id')
                    ->orderBy('members', 'desc')
                    ->paginate(env('COMMAND_LIMIT'))
            );
        }

        // Сортировка по количеству игроков(численно) и дате регистрации

        else if ($data['players'] != 0 && $data['sort'] == 0 && $data['name'] == NULL) {
            $data = array(
                'commands' => DB::table('commands')
                    ->where('capitan', '!=', 0)
                    ->leftJoin('users', 'users.command', '=', 'commands.id')
                    ->select('commands.id', 'commands.name', 'commands.avatar', 'commands.rating', DB::raw('count(users.command) as members'))
                    ->havingRaw('count(users.command) = '.$data['players'])
                    ->orderBy('commands.created_at', 'asc')
                    ->groupBy('commands.id')
                    ->paginate(env('COMMAND_LIMIT'))
            );
        }

        // Сортировка по количеству игроков(численно) и алфавитном порядке

        else if ($data['players'] != 0 && $data['sort'] == 1 && $data['name'] == NULL) {
            $data = array(
                'commands' => DB::table('commands')
                    ->where('capitan', '!=', 0)
                    ->leftJoin('users', 'users.command', '=', 'commands.id')
                    ->select('commands.id', 'commands.name', 'commands.avatar', 'commands.rating', DB::raw('count(users.command) as members'))
                    ->havingRaw('count(users.command) = '.$data['players'])
                    ->orderBy('commands.name', 'asc')
                    ->groupBy('commands.id')
                    ->paginate(env('COMMAND_LIMIT'))
            );
        }

        // Сортировка по количеству игроков(численно) и количеству в общем

        else if ($data['players'] != 0 && $data['sort'] == 2 && $data['name'] == NULL) {
            $data = array(
                'commands' => DB::table('commands')
                    ->where('capitan', '!=', 0)
                    ->leftJoin('users', 'users.command', '=', 'commands.id')
                    ->select('commands.id', 'commands.name', 'commands.avatar', 'commands.rating', DB::raw('count(users.command) as members'))
                    ->havingRaw('count(users.command) = '.$data['players'])
                    ->orderBy('commands.name', 'asc')
                    ->groupBy('commands.id')
                    ->paginate(env('COMMAND_LIMIT'))
            );
        }

        // Сортировка по дате регистрации и имени

        else if($data['players'] == 0 && $data['sort'] == 0 && $data['name'] != NULL){
            $data = array(
                'commands' => DB::table('commands')
                    ->where('capitan', '!=', 0)
                    ->leftJoin('users', 'users.command', '=', 'commands.id')
                    ->select('commands.id', 'commands.name', 'commands.avatar', 'commands.rating', DB::raw('count(users.command) as members'))
                    ->where('commands.name', 'like', '%'.$data['name'].'%')
                    ->orderBy('commands.created_at', 'asc')
                    ->groupBy('commands.id')
                    ->paginate(env('COMMAND_LIMIT'))
            );
        }

        //  Сортировка по алфавиту и имени

        else if($data['players'] == 0 && $data['sort'] == 1 && $data['name'] != NULL){
            $data = array(
                'commands' => DB::table('commands')
                    ->where('capitan', '!=', 0)
                    ->leftJoin('users', 'users.command', '=', 'commands.id')
                    ->select('commands.id', 'commands.name', 'commands.avatar', 'commands.rating', DB::raw('count(users.command) as members'))
                    ->where('commands.name', 'like', '%'.$data['name'].'%')
                    ->orderBy('commands.name', 'asc')
                    ->groupBy('commands.id')
                    ->paginate(env('COMMAND_LIMIT'))
            );
        }

        // Сортировка по количеству и имени

        else if($data['players'] == 0 && $data['sort'] == 2 && $data['name'] != NULL){
            $data = array(
                'commands' => DB::table('commands')
                    ->where('capitan', '!=', 0)
                    ->leftJoin('users', 'users.command', '=', 'commands.id')
                    ->select('commands.id', 'commands.name', 'commands.avatar', 'commands.rating', DB::raw('count(users.command) as members'))
                    ->where('commands.name', 'like', '%'.$data['name'].'%')
                    ->orderBy('members', 'desc')
                    ->groupBy('commands.id')
                    ->paginate(env('COMMAND_LIMIT'))
            );
        }

        // Сортировка по количеству игроков(число), дате и имени

        else if($data['players'] != 0 && $data['sort'] == 0 && $data['name'] != NULL) {
            $data = array(
                'commands' => DB::table('commands')
                    ->where('capitan', '!=', 0)
                    ->leftJoin('users', 'users.command', '=', 'commands.id')
                    ->select('commands.id', 'commands.name', 'commands.avatar', 'commands.rating', DB::raw('count(users.command) as members'))
                    ->where('commands.name', 'like', '%'.$data['name'].'%')
                    ->havingRaw('count(users.command) = '.$data['players'])
                    ->orderBy('commands.created_at', 'asc')
                    ->groupBy('commands.id')
                    ->paginate(env('COMMAND_LIMIT'))
            );
        }

        // Сортировка по количеству игроков(число), алфавиту и имени

        else if($data['players'] != 0 && $data['sort'] == 1 && $data['name'] != NULL) {
            $data = array(
                'commands' => DB::table('commands')
                    ->where('capitan', '!=', 0)
                    ->leftJoin('users', 'users.command', '=', 'commands.id')
                    ->select('commands.id', 'commands.name', 'commands.avatar', 'commands.rating', DB::raw('count(users.command) as members'))
                    ->where('commands.name', 'like', '%'.$data['name'].'%')
                    ->havingRaw('count(users.command) = '.$data['players'])
                    ->orderBy('commands.name', 'asc')
                    ->groupBy('commands.id')
                    ->paginate(env('COMMAND_LIMIT'))
            );
        }

        // Сортировка по количеству игроков(число), количеству в общем и имени

        else if($data['players'] != 0 && $data['sort'] == 2 && $data['name'] != NULL) {
            $data = array(
                'commands' => DB::table('commands')
                    ->where('capitan', '!=', 0)
                    ->leftJoin('users', 'users.command', '=', 'commands.id')
                    ->select('commands.id', 'commands.name', 'commands.avatar', 'commands.rating', DB::raw('count(users.command) as members'))
                    ->where('commands.name', 'like', '%'.$data['name'].'%')
                    ->havingRaw('count(users.command) = '.$data['players'])
                    ->orderBy('commands.name', 'asc')
                    ->groupBy('commands.id')
                    ->paginate(env('COMMAND_LIMIT'))
            );
        }

        if (!$data) {
            $status = "error";
        }

        return response()->json([
            'status' => $status,
            'message' => $data
        ]);
    }


    /**
     * @return дамп из БД
     * Принимает параметры из фильтра комманд (Количество игроков, сортировку по дате, алфавиту и тд, по имени)
     * Данная функция для тестов, позже она будет удалена
     */
    public function filter(Request $request) {
        $data = $request->all();
        if(isset($data['sort']) && $data['sort'] == 2 && $data['players'] == 0) {
            $data = array(
                'commands' => DB::table('commands')
                    ->where('capitan', '!=', 0)
                    ->leftJoin('users', 'users.command', '=', 'commands.id')
                    ->select('commands.id', 'commands.name', 'commands.avatar', DB::raw('count(users.command) as members'))
                    ->groupBy('commands.id')
                    ->orderBy('members', 'asc')
                    ->paginate(env('COMMAND_LIMIT'))
            );
            return var_dump($data['commands']);
        }else{
            return 'Not data';
        }
        return "Error";
    }

    /**
    * @return view commands.create
    * Возвращает страницу с созданием команды
    */
    public function create(){
        if(!Auth::user()) {
            return redirect('auth/steam');
        }

        if(Auth::user()->command){
            return redirect()->route('command');
        }

        $id = Auth::user()->id;
        $contacts_get = User::where('id',$id)->first();
        $contacts_get = $contacts_get->contacts;

        if ($contacts_get !== NULL){
            $contact = json_decode($contacts_get);

            $contacts = [
                'skype' => $contact->skype,
                'phone' => $contact->phone,
                'telegram' => $contact->telegram,
                'vk' => $contact->vk,
                'fb' => $contact->fb,
                'discord' => $contact->discord
            ];

            return view('commands/create',$contacts);
        }
    }


    /**
    * @return view command.edit
    * Возвращает страницу редактирования комманды
    */
    public function edit(){
        if(!Auth::user()) {
            return redirect('auth/steam');
        }
        
        $id = Auth::user()->id;
        $contacts_get = User::where('id',$id)->first();
        $contacts = $contacts_get->contacts;
        $command_data = Commands::where('capitan', $id)->first();
        
        if (!$command_data) {
            return redirect('commands');
        }
        
        $name = $command_data->name;
        $teammates = User::where('command',$command_data->id)->get();
        $count = $teammates->count();

        if ($contacts_get !== NULL){
            
            $contacts = json_decode($contacts);
            $contacts = [
                'skype' => $contacts->skype,
                'phone' => $contacts->phone,
                'telegram' => $contacts->telegram,
                'vk' => $contacts->vk,
                'fb' => $contacts->fb,
                'discord' => $contacts->discord,
                'name' => $name,
                'greeting' => $command_data->greeting,
                'qiwi' => $command_data->qiwi,
                'img' => $command_data->avatar,
                'count' => $count,
                'teammates' => $teammates,
                'capitan' => $command_data->capitan
            ];
            
            return view('commands/edit', $contacts);
            
        }
    }

    /**
    * @param object $request - данные POST запроса с view command.create
    * @return redirect('command')
    * Создаёт новую команду и заносит информацию о ней в БД
    */
    public function save(Request $request){
        if(!Auth::user()) { // TODO убрать. Сделать middleware или перенести в функцию (DRY)
            return redirect('auth/steam');
        }

        // TODO убрать статичные значения 1000, 6 и т.д., перенести в ENV конфигурацию
        if($request->input('vk') != null || $request->input('skype') != null || $request->input('discord') != null){

            $validator = Validator::make($request->all(), [
                'name' => 'min:'. env('MIN_COMMANDNAME_LENGTH') .'|max:'. env('MAX_COMMANDNAME_LENGTH').'|unique:commands|required|regex:/(^([A-Za-z0-9а-яА-Я\s]+)(\d+)?$)/u',
                'avatar' => 'dimensions:min_width=' . env('MIN_FILE_WIDTH') . '|dimensions:min_height='. env('MIN_FILE_HEIGHT') .'|dimensions:max_width='.env('MAX_FILE_WIDTH').'|dimensions:max_height='. env('MAX_FILE_HEIGHT'),
                'greeting' => 'max:1000',
                'qiwi' => 'min:' . env('QIWI_LENGTH') . '|max:30|required',
                'skype' => 'max:30',
                'phone' => 'max:30',
                'telegram' => 'max:30',
                'vk' => 'max:100',
                'fb' => 'max:100',
                'discord' => 'max:100'
            ]);

        }else{

            return back()
                ->withErrors("Необходимо заполнить хотя бы одно из полей vk, discord или skype")
                ->withInput();
                
        }
        
        
        if ($validator->fails()) {
            return redirect('commands/create')
                ->withErrors($validator)
                ->withInput();
        }
        
        $data = $request->all();
        $id = Auth::user()->id;
        $name = $data['name'];
        $greeting = $data['greeting'];
        
        if($data['qiwi']) {
            $qiwi = $data['qiwi'];
        }
        
        $file = $request->file('avatar');
        $fileName = 'logo.png';

        if($file != ''){

            // TODO цифры в ENV
            $validator = $file->isValid();
            $size = ($file->getClientSize()) / 1024;

            if($size < env('MAX_FILE_SIZE_KB') && $validator){
                $destinationPath = 'storage';
                $extension = $file->getClientOriginalExtension();
                $fileName = md5(date('m.d.y H:i:s')). '.' . $extension;
                $file->move($destinationPath, $fileName);
            }

        }
        else{
            $fileName = "logo.png";
        }
        
        $command_check = Commands::where('name', $name)->first();
        
        if (is_null($command_check)) {
            $token = md5(date('m.d.y H:i:s').$name);
            
            $model = Commands::create([
                'name' => $name,
                'greeting' => $greeting,
                'qiwi'  => $qiwi,
                'avatar' => $fileName,
                'balance' => 0,
                'capitan' => $id,
                'token' => $token
            ]);

            $data = array(
                'skype' => $request->input('skype'),
                'phone' => $request->input('phone'),
                'telegram' => $request->input('telegram'),
                'vk' => $request->input('vk'),
                'fb' => $request->input('fb'),
                'discord' => $request->input('discord'),
            );

            $data = json_encode($data);
            $command_id = $model->id;

            User::where('id', $id)->update([
                'command' => $command_id,
                'contacts' => $data
            ]);
        }
        
        return redirect('command');
    }

    /**
    * @param object $request - данные POST запроса
    * @return redirect('command')
    * Редактирует уже существующую комманду и изменяет информацию о ней в БД
    */
    public function edit_command(Request $request){

        if(!Auth::user()) {// TODO убрать. Сделать middleware
            return redirect('auth/steam');
        }

        $commands = DB::table('tournament_grids')
                    ->where('tournament_id', $this->tournamentId)
                    ->where('stage_id', $this->stageId)
                    ->first();

        if($commands !== NULL) {
            $name = $request->input('name');
            $commandNameTable = DB::table('commands')
                                ->where('id', Auth::user()->command)
                                ->first()->name;
            if($name != $commandNameTable){

                return back()
                    ->withErrors("Нельзя менять имя во время того, как команда находится в турнире.")
                    ->withInput();
            }
        }

        if($request->input('vk') != null || $request->input('skype') != null || $request->input('discord') != null){

            $validator = Validator::make($request->all(), [
                'name' => 'min:3|max:20|required|regex:/(^([A-Za-z0-9а-яА-Я\s]+)(\d+)?$)/u',
                'avatar' => 'dimensions:min_width=' . env('MIN_FILE_WIDTH') . '|dimensions:min_height='. env('MIN_FILE_HEIGHT') .'|dimensions:max_width='.env('MAX_FILE_WIDTH').'|dimensions:max_height='. env('MAX_FILE_HEIGHT'),
                'greeting' => 'max:1000',
                'skype' => 'max:30',
                'phone' => 'max:30',
                'telegram' => 'max:30',
                'vk' => 'max:100',
                'fb' => 'max:100',
                'discord' => 'max:100'
            ]);

        }else{

            return back()
                ->withErrors("Любое из полей vk, discord или skype должны быть заполнены")
                ->withInput();

        }

        if ($validator->fails()) {
            return redirect('commands/edit')
                        ->withErrors($validator)
                        ->withInput();
        }

        $id = Auth::user()->id;
        $command = Commands::where('capitan', $id)->first();

        $command_name = $request->input('name');
        $exist = Commands::where('name', $command_name)
            ->where('capitan', '!=', $id)->first();

        if ($exist || !$command) {
            return redirect('command');
        }

        $data = $request->all();
        $capitan_id = $id;
        $name = $data['name'];
        $greeting = $data['greeting'];
        $file = $request->file('avatar');

        if ($file != ''){

            $validator = $file->isValid();
            $size = ($file->getClientSize()) / 1024;

            if($size < env('MAX_FILE_SIZE_KB') && $validator){
                $destinationPath = 'storage';
                $extension = $file->getClientOriginalExtension();
                $fileName = md5(date('m.d.y H:i:s')). '.' . $extension;
                $file->move($destinationPath, $fileName);
            }

        }
        else{
            $fileName = $command->avatar;
        }


        if (!is_null($command)) {

            if (isset($data['capitan'])){
                $change_capitan = $data['capitan'];
                $check = User::where('id', $change_capitan)->first();

                if($check->command == $command->id) {
                    $capitan_id = $change_capitan;
                }
            }

            if (isset($data['delete'])){
                $delete_players = $data['delete'];
                $i = 0;

                while(isset($delete_players[$i])){
                    $delete_id = $delete_players[$i];
                    User::where('id', $delete_id)->update(['command' => NULL]);
                    $i++;
                }
            }

            Commands::where('capitan', $id)->update(array(
                'name' => $name,
                'greeting' => $greeting,
                'avatar' => $fileName,
                'capitan' => $capitan_id,
            ));

            $data = array(
                'skype' => $request->input('skype'),
                'phone' => $request->input('phone'),
                'telegram' => $request->input('telegram'),
                'vk' => $request->input('vk'),
                'fb' => $request->input('fb'),
                'discord' => $request->input('discord'),
            );

            $data = json_encode($data);

            User::where('id', $capitan_id)->update(array(
                'contacts' => $data
            ));
        }

        return redirect('command'); 
    }

    /**
    * @param string $token - случайно сгенерированный токен
    * @return some
    * Обрабатывает ссылку с токеном на вступлениие в комманду
    */
    public function add_teammate($token){
        if(!Auth::user()) {
            return redirect('auth/steam'); // TODO в middleware или функцию
        }

        $id = Auth::user()->id;
        $command_info = Commands::where('token', $token)->first();
        $command_id = $command_info->id;
        $count = User::where('command',$command_id)->count();

        if (!$count) {
            return redirect('/commands')->with('message', 'Команда расформирована! Приглашение недействительно!');
        }

        if(!is_null($command_info) && !Auth::user()->command && $count < env('MAX_COMMAND_POPULATION')){
            User::where('id', $id)->update(['command' => $command_id]);
            return redirect('/command')->with('message', 'Вы вступили в команду!');
        }
        else if (Auth::user()->command) {
            return redirect('/commands')->with('message', 'Вы уже состоите в команде!');
        }
        else if ($count >= env('MAX_COMMAND_POPULATION')) {
            return redirect('/commands')->with('message', 'Команда заполнена');
        }
        else{
            return redirect('/commands')->with('message', 'Приглашение недействительно!');
        }

        return $count;
    }


    /**
     * Удаляем игрока из команды по его запросу
     * @return \Illuminate\Http\RedirectResponse
     */
    public function commandExit()
    {
        $id = Auth::user()->id;

        if (DB::table('commands')->where('capitan', $id)->first()) {
            return redirect('/command')->with('message', 'Капитан не может покинуть команду без расформирования команды');
        };

        DB::table('users')
            ->where('id', $id)
            ->update([
                'command' => 0
            ]);

        return redirect('/commands')->with('message', 'Вы вышли из команды');
    }

    /**
     * Удаляем команду по запросу капитана
     * @return \Illuminate\Http\RedirectResponse
     */
    public function commandRemove()
    {
        $id = Auth::user()->id;

        // команда капитана
        $command = DB::table('commands')
            ->where('capitan', $id)
            ->first();

        if ($command) {
            // удаляем из команды всех игроков и себя
            DB::table('users')
                ->where('command', $command->id)
                ->update([
                    'command' => 0
                ]);

            DB::table('commands')
                ->where('id', $command->id)
                ->update([
                    'capitan' => 0
                ]);

            return redirect('/commands')->with('message', 'Ваша команда была расформирована');
        }
        else {
            return redirect('/command')->with('message', 'Расформировать команду может только капитан');
        }


    }

}