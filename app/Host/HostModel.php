<?php

namespace App\Host;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\User\UserModel as User;
use App\System\Level\LevelModel as Level;
use App\Host\File\FileModel as File;
use App\Host\Folder\FolderModel as Folder;

class HostModel extends Model
{
    protected $table = 'hosts';

    protected $fillable = [
		'user_id',
		'password',
        'host_name',
        'location',
        'motd',
        'notes',
        'org',
		'ip',
		'active',
        'level_id'
    ];

    public $timestamps = true;

    // A host can have many files
    public function files()
    {
        return $this->BelongsToMany(File::class, 'host_file', 'host_id', 'file_id');
    }

    public function file($data)
    {
        return $this->files()->where('files.id', $data)->orWhere('file_name', $data)->first();
    }

    // A host can have many folders
    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

    public function users(): BelongsToMany
    {
        return $this->BelongsToMany(User::class, 'host_user', 'host_id', 'user_id');
    }
    
    public function host($host)
    {
        return $this->users()->where('host_id', $host)->first();
    }

    public function user($user)
    {
        return $this->users()->where('user_id', $user)->first();
    }

    public function nodes(): BelongsToMany
    {
        return $this->BelongsToMany(HostModel::class, 'host_node', 'host_id', 'node_id');
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