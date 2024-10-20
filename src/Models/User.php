<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\Host;

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

    public function hosts(): BelongsToMany
    {
        return $this->BelongsToMany(Host::class);
    }

    public function host($host)
    {
        return $this->hosts()->where('host_id', $host)->first();
    }

}