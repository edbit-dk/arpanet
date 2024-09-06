<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
		'email',
		'username',
		'password',
		'firstname',
		'lastname',
		'fullname',
        'active',
        'level_id',
        'xp',
        'rep',
        'last_login'
    ];
}