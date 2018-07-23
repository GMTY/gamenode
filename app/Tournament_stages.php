<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tournament_stages extends Model
{
    protected $fillable = [
    	'tournament_id',
        'stage', 
        'title',
        'date',
    ];
}
