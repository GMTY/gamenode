<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tournament_grids extends Model
{
    protected $fillable = [
        'id_tournament', 
        'stage_id',
        'command_id',
        'order',
        'result',
        'id_game'
    ];
}
