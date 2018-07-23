<?php

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$this->call(UsersTableSeeder::class);
    	$this->call(CommandsTableSeeder::class);
    	$this->call(TournamentTableSeeder::class);
        $this->call(TournamentStageTableSeeder::class);
        $this->call(NewsTableSeeder::class);
    }
}
