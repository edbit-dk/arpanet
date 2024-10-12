<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\User;

class Server extends Model
{
    public $timestamps = true;

    public function users(): BelongsToMany
    {
        return $this->BelongsToMany(User::class, 'server_user');
    }
}