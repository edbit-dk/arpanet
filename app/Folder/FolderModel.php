<?php

namespace App\Folder;

use App\AppModel;

use App\Host\HostModel as Host;
use App\File\FileModel as File;
use App\User\UserModel as User;

class FolderModel extends AppModel
{
    protected $table = 'folders';

    protected $guarded = [];

    protected $maps = [
        'foldername' => 'foldername',
        'content' => 'content',
        'folder_id' => 'folder_id',
        'host_id' => 'host_id',
        'user_id' => 'user_id',
    ];

    // A folder belongs to a user (owner)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A folder belongs to a host
    public function host()
    {
        return $this->belongsTo(Host::class);
    }

    // Check if the user is the owner of the folder
    public function isOwnedBy($user)
    {
        return $this->user_id == $user->id;
    }

    // A folder can have many files
    public function files()
    {
        return $this->hasMany(File::class);
    }

    // A folder can have many subfolders (self-referencing one-to-many relationship)
    public function subfolders()
    {
        return $this->hasMany(FolderModel::class, 'parent_id');
    }

    // A folder can belong to a parent folder
    public function parentFolder()
    {
        return $this->belongsTo(FolderModel::class, 'parent_id');
    }
}
