<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\Server;

class User extends Model
{
    public $timestamps = true;
    
    protected $fillable = [
		'email',
		'username',
		'password',
        'access_code',
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