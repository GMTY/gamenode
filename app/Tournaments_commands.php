<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tournaments_commands extends Model
{
    protected $fillable = [
        'tournament_id',
        'command_id'
    ];
}
