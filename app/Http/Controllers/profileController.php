<?php

namespace App\Http\Controllers;

use Auth;
use Validator;

use App\User as User;
use App\Commands as Commands;

use Illuminate\Http\Request;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use DB;


class ProfileController extends Controller
{

    public function __construct() {
        $user = User::where('id', 129)->first();
        Auth::login($user, true);
    }
    /**
     * @param int $id - ID профиля
     * @return mixed
     * Выводит игрока по ID
     */
    public function index($id = 0 ) {
        if(!Auth::user() && $id == 0) {
            return redirect('auth/steam');
        }

        $hide = false;
        $auth = 0;
        $command_name = '';
        $command_id = 0;

        if ($id == 0) {
            $id = Auth::user()->id;
            $auth = 1;
        }

        $user = User::where('id', $id)->first();

        if (!$user) {
            return redirect()->route('profile');
        }

        if ($id != Auth::user()->id) {
            $hide = true;
        }

        $contacts_get = $user->contacts;
        $contact = json_decode($contacts_get);

        if ($user->command) {
            $command_info = Commands::where('id', $user->command)->first();

            if ($command_info) {
                $command_id = $command_info->id;
                $command_name = $command_info->name;
            }

        }

        $payments = DB::table('payments_history')
            ->where('user_id', $id)
            ->leftJoin('commands', 'payments_history.command_id', '=', 'commands.id')
            ->select('payments_history.*', 'commands.name as command_name')
            ->orderBy('date', 'DESC')
            ->get();

        $data = [
            'username' => $user->username,
            'steamid' => $user->steamid,
            'avatar' => $user->avatar,
            'skype' => $contact->skype,
            'phone' => $contact->phone,
            'telegram' => $contact->telegram,
            'vk' => $contact->vk,
            'fb' => $contact->fb,
            'discord' => $contact->discord,
            'command' => $command_name,
            'auth' => $auth,
            'commandID' => $command_id,
            'hide' => $hide,
            'rating' => $user->rating,
            'payments' => $payments
        ];

        return view('profile/index', $data);
    }


    /**
     * @return mixed
     * Выводит список всех игроков с лимитом из конфига
     */
    public function list() {
        $users = User::limit(env('PROFILES_LIMIT'))
            ->orderBy('id', 'DESC')
            ->get();

        if (!$users) {
            return redirect()->route('/');
        }

        return view('profile/list',
            [
                'users' => $users
            ]
        );
    }

    /**
     * @return mixed json
     * Получает профили с учетом страницы (для AJAX)
     */
    public function getProfiles() {
        $status = "success";

        $data = array(
            'users' => DB::table('users')
                ->orderBy('id', 'DESC')
                ->select('id', 'username', 'avatar')
                ->paginate(env('PROFILES_LIMIT'))
        );

        if (!$data) {
            $status = "error";
        }

        return response()->json([
            'status' => $status,
            'message' => $data
        ]);
    }


    // Выводит страницу с редактированием профиля
    public function edit(){
        if(!Auth::user()) { // TODO убрать, добавить middleware
            return redirect('auth/steam');
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
            return view('profile/edit', $contacts); 
        }
        return view('profile/edit');
    }
    // Сохраняет изменённые или вносит новые контакты о пользователе
    public function save(Request $request){
        if(!Auth::user()) { // TODO убрать, добавить middleware
            return redirect('auth/steam');
        }

        $id = Auth::user()->id;
        $data_post = $request->all();
        $count = count($data_post);

        if ($count != 7) { // TODO ЧТО ЭТО за проверка??? (Проверка того, что пользователь в html не удалил какое-то поля, т.к. мы создаём по полям json)
            return redirect('/profile/edit');
        }

        if($request->input('vk') != null || $request->input('skype') != null || $request->input('discord') != null){
            $validator = Validator::make($request->all(), [
                'skype' => 'max:30',
                'phone' => 'max:30',
                'telegram' => 'max:30',
                'vk' => 'max:100',
                'fb' => 'max:100',
                'discord' => 'max:100'
            ]);
        }else{
            return redirect('profile/edit')
                ->withErrors("Любое из полей vk, discord или skype должны быть заполнены")
                ->withInput();
        }
        

        if ($validator->fails()) {
            return redirect('profile/edit')
                ->withErrors("Не введено поле")
                ->withInput();
        }

        $data = $request->all();
        $data = json_encode($data);

        User::where('id', $id)->update(array(
           'contacts' => $data
        ));

        return redirect('profile/');
    }
}
