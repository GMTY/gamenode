<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Commands extends Model
{
    protected $fillable = [
        'name', 
        'greeting', 
        'qiwi', 
        'balance', 
        'avatar', 
        'capitan', 
        'token',
    ];
}
