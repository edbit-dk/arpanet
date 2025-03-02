<?php

namespace App\File;

use App\AppModel;

use App\User\UserModel as User;
use App\Host\HostModel as Host;
use App\Folder\FolderModel as Folder;

class FileModel extends AppModel
{
    protected $table = 'files';

    protected $guarded = [];

    protected $maps = [
        'filename' => 'filename',
        'content' => 'content',
        'folder_id' => 'folder_id',
        'host_id' => 'host_id',
        'user_id' => 'user_id',
    ];

    // A file belongs to a user (owner)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A file belongs to a host
    public function host()
    {
        return $this->belongsTo(Host::class);
    }

    // A file belongs to a folder
    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
}
