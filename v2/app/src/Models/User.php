<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Model
{
    public $timestamps = true;
    
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

    public function servers(): BelongsToMany
    {
        return $this->belongsToMany(Server::class, 'server_user');
    }
}