<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\User;

class Host extends Model
{
    protected $fillable = [
		'username',
		'password',
        'name',
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

    public function nodes(): BelongsToMany
    {
        return $this->BelongsToMany(Host::class, 'host_node', 'node_id', 'host_id');
    }

    public function node($host)
    {
        return $this->nodes()->where('node_id', $host)->first();
    }
}