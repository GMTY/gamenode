<?php

use Illuminate\Database\Seeder;

use App\News;
use Carbon\Carbon;

class NewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	for ($i=0; $i < 10; $i++) { 
    		News::create([
    			'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
    			'date' => Carbon::now()

    		]);
    	}
    }
}
