<?php

namespace App\Http\Controllers;

use Invisnik\LaravelSteamAuth\SteamAuth;
use App\User;
use Auth;

class AuthController extends Controller
{
    /**
     * @var SteamAuth
     */
    private $steam;

    public function __construct(SteamAuth $steam)
    {
        $this->steam = $steam;
    }

    public function login()
    {
        if ($this->steam->validate()) {
            $info = $this->steam->getUserInfo();
            if (!is_null($info)) {
                $user = User::where('steamid', $info->steamID64)->first();

                if (is_null($user)) {
                    $user = User::create([
                        'username' => $info->personaname,
                        'avatar'   => $info->avatarfull,
                        'steamid'  => $info->steamID64,
                        'status' => 1,
                        'contacts' => '{"skype":null,"phone":null,"telegram":null,"vk":null,"fb":null,"_token":null,"discord":null}'
                    ]);
                }
                Auth::login($user, true);
                
                return redirect('/'); 
            } else {
                return "Bad!";
            }
        }
        return $this->steam->redirect(); 
    }

    public function logout(){
        Auth::logout();
        return redirect('/');
    }
}