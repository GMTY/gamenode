<?php

use Illuminate\Database\Seeder;

use App\Tournaments as Tournament;
use App\Tournaments_commands as Teams;
use App\Commands as Commands;

class TournamentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=1; $i <= 32; $i++) {
			$command = Commands::where('id',$i)->first();
			Teams::create([
    			'tournament_id' => 2,
    			'command_id' => $command->id
    		]);
        }

        Tournament::create([
            'title' => 'Первый турнир',
            'content' => '',
            'start' => '2017-12-23 18:20:00',
            'end' => '2017-12-24 00:00:00',
            'status' => 1 // закончен
        ]);

        Tournament::create([
            'title' => 'Второй турнир',
            'content' => '',
            'start' => '2017-12-25 10:20:00',
            'end' => '2017-12-31 00:00:00'
        ]);
    }
}	
