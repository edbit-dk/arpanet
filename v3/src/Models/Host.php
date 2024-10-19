<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Models\User;
use App\Models\Level;

class Host extends Model
{
    protected $fillable = [
		'username',
		'password',
        'name',
        'org',
		'ip',
		'status',
		'location',
        'nodes',
        'level_id'
    ];

    public $timestamps = true;

    public function users(): BelongsToMany
    {
        return $this->BelongsToMany(User::class);
    }

    public function user($user)
    {
        return $this->users()->where('user_id', $user)->first();
    }

    public function nodes(): BelongsToMany
    {
        return $this->BelongsToMany(Host::class, 'host_node', 'host_id', 'node_id');
    }

    public function node($host)
    {
        return $this->nodes()->where('node_id', $host)->first();
    }

    public function level(): HasOne   
    {
        return $this->hasOne(Level::class, 'id', 'level_id');
    }


}