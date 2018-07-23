<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 
        'avatar', 
        'steamid', 
        'status', 
        'command', 
        'contacts',
        'is_admin',
        'rating'
    ];

    public function isAdmin()
    {
        return $this->is_admin; // поле is_admin в таблице users
    }
}
