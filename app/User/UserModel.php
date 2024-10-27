<?php

namespace App\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Host\HostModel as Host;
use App\Host\File\FileModel as File;
use App\Host\Folder\FolderModel as Folder;

class UserModel extends Model
{
    protected $table = 'users';
    public $timestamps = true;
    
    protected $fillable = [
		'email',
		'user_name',
		'password',
        'access_code',
		'firstname',
		'lastname',
        'active',
        'level_id',
        'xp',
        'rep',
        'last_login'
    ];

    // A user can have many files
    public function files()
    {
        return $this->hasMany(File::class);
    }

    // A user can have many folders
    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

    public function hosts(): BelongsToMany
    {
        return $this->BelongsToMany(Host::class);
    }

    public function host($host)
    {
        return $this->hosts()->where('host_id', $host)->first();
    }

}