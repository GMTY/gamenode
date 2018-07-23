<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Command_stat extends Model
{
    protected $fillable = [
    	'id_command',
        'win_matches',
        'all_matches' 
    ];
}
