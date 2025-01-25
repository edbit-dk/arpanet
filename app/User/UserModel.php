<?php

namespace App\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Host\HostModel as Host;
use App\Host\File\FileModel as File;
use App\Host\Folder\FolderModel as Folder;
use App\System\Mission\MissionModel as Mission;
use App\System\Email\EmailModel as Email;

class UserModel extends Model
{
    protected $table = 'users';
    public $timestamps = true;
    
    protected $fillable = [
		'email',
		'user_name',
		'password',
        'access_code',
		'fullname',
        'role',
        'active',
        'level_id',
        'ip',
        'xp',
        'last_login'
    ];

    // A user can have many files
    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function emails()
    {
        return $this->hasMany(Email::class);
    }

    // A user can have many folders
    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

    public function hosts(): BelongsToMany
    {
        return $this->BelongsToMany(Host::class, 'host_user', 'user_id', 'host_id');
    }

    public function host($host)
    {
        return $this->hosts()->where('host_id', $host)->first();
    }

    public function missions() 
    {
        return $this->belongsToMany(Mission::class, 'user_missions')
                    ->withPivot('status', 'started_at', 'completed_at')
                    ->withTimestamps();
    }

}