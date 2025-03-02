<?php

namespace App\Host;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\User\UserModel as User;
use App\Level\LevelModel as Level;
use App\File\FileModel as File;
use App\Folder\FolderModel as Folder;

use App\AppModel;

class HostModel extends AppModel
{
    protected $table = 'hosts';

    public $timestamps = true;

    protected $guarded = [];

    protected $maps = [
        'id' => 'id',
		'user_id' => 'user_id',
        'password' => 'password',
        'hostname' => 'hostname',
        'welcome' => 'welcome',
        'motd' => 'motd',
        'notes' => 'notes',
        'org' => 'org',
		'ip' => 'ip',
		'active' => 'active',
        'level_id' => 'level_id'
    ];

    // A host can have many files
    public function files()
    {
        return $this->hasMany(File::class, 'host_id');
    }

    public function file($name, $folder_id)
    {
        return $this->files()->where('folder_id', $folder_id)->where('filename', $name)->first();
    }

    // A host can have many folders
    public function folders()
    {
        return $this->hasMany(Folder::class, 'host_id');
    }

    public function users(): BelongsToMany
    {
        return $this->BelongsToMany(User::class, 'host_user', 'host_id', 'user_id')->withPivot('last_session');
    }
    
    public function host($host)
    {
        return $this->where('id', $host)->first();
    }

    public function user($user)
    {
        return $this->users()->where('user_id', $user)->first();
    }

    public function nodes(): BelongsToMany
    {
        return $this->BelongsToMany(HostModel::class, 'host_node', 'host_id', 'node_id');
    }

    public function hosts()
    {
        return $this->BelongsToMany(HostModel::class, 'host_node', 'node_id', 'host_id');
    }

    public function connections()
    {
        $connections = $this->nodes;
        
        if(!$connections->isEmpty()) {
            return $connections;
        }

        $connections = $this->hosts;

        if(!$connections->isEmpty()) {
            return $connections;
        }
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