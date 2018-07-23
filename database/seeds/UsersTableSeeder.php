<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User as User;
use App\Profile_stat as Profile_stat;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		User::create([
			'username' => 'Migoci',
			'avatar'   => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg',
			'steamid'  => 76561198443041633,
			'command' => 1,
			'status' => 1,
			'contacts' => '{"skype":"skype-login-example","phone":"+7-967-1234567","telegram":null,"vk":null,"fb":null,"_token":null,"discord":null}',
			'is_admin' => true
		]);

    	for ($i=2; $i <= 128; $i++) {
    		if($i % 3 == 1) {
                User::create([
                    'username' => 'Имя игрока N'. $i . rand(10, 100),
                    'avatar'   => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/13/1397deefabc0ac3ca85e325c636f5086c90fb44d_full.jpg',
                    'steamid'  => str_random(10),
                    'command' => $i,
                    'status' => 1,
                    'rating' => rand(10, 100),
                    'contacts' => '{"skype":"skype-login-example","phone":"+7-967-1234567","telegram":null,"vk":null,"fb":null,"_token":null,"discord":null}'
                ]);
            } else if($i % 3 == 2) {
                User::create([
                    'username' => 'Имя игрока N'. $i . rand(10, 100),
                    'avatar'   => 'http://dota2.loc/img/char3.png',
                    'steamid'  => str_random(10),
                    'command' => $i,
                    'status' => 1,
                    'rating' => rand(10, 100),
                    'contacts' => '{"skype":"skype-login-example","phone":"+7-967-1234567","telegram":null,"vk":null,"fb":null,"_token":null,"discord":null}'
                ]);
            } else {
                User::create([
                    'username' => 'Имя игрока N'. $i . rand(10, 100),
                    'avatar'   => 'http://dota2.loc/img/char2.png',
                    'steamid'  => str_random(10),
                    'command' => $i,
                    'status' => 1,
                    'rating' => rand(10, 100),
                    'contacts' => '{"skype":"skype-login-example","phone":"+7-967-1234567","telegram":null,"vk":null,"fb":null,"_token":null,"discord":null}'
                ]);
            }
    	}

    }
}
