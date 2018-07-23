<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile_stat extends Model
{
    protected $fillable = [
    	'id_profile',
        'win_matches',
        'all_matches' 
    ];
}
