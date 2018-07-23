<?php

use Illuminate\Database\Seeder;
use App\Commands as Commands;
use App\Command_stat as Command_stat;

class CommandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=1; $i < 128; $i++) {
        	Commands::create([
    			'name' => 'Команда N' . $i . rand(5, 15),
    			'greeting'   => 'Lorem Ipsum - это текст-"рыба", часто используемый в печати и вэб-дизайне. Lorem Ipsum является стандартной "рыбой" для текстов на латинице с начала XVI века. В то время некий безымянный печатник создал большую коллекцию размеров и форм шрифтов, используя Lorem Ipsum для распечатки образцов. Lorem Ipsum не только успешно пережил без заметных изменений пять веков, но и перешагнул в электронный дизайн. Его популяризации в новое время послужили публикация листов Letraset с образцами Lorem Ipsum в 60-х годах и, в более недавнее время, программы электронной вёрстки типа Aldus PageMaker, в шаблонах которых используется Lorem Ipsum.',
    			'qiwi'  => rand(100,10000000),
    			'balance' => 200,
    			'avatar' => 'logo.png',
    			'capitan' => $i,
    			'rating' => rand(10, 10000),
    			'token' => str_random(9)
    		]);


		    Command_stat::create([
                'command_id' => $i,
                'win_matches' => random_int(0, 100),
                'all_matches' => random_int(100, 300)
            ]);

        }
        Commands::create([
            'name' => 'Nagibatori',
            'greeting'   => 'Здравствуйте!',
            'qiwi'  => rand(6,12),
            'balance' => 200,
            'avatar' => 'logo.png',
            'capitan' => 128,
            'token' => str_random(9)
        ]);
    }
}
