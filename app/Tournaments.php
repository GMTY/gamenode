<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tournaments extends Model
{
    protected $fillable = [ 
        'title',
        'content',
        'start',
        'end'
    ];
}
