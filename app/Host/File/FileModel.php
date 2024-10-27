<?php

namespace App\Host\File;

use Illuminate\Database\Eloquent\Model;

use App\User\UserModel as User;
use App\Host\HostModel as Host;
use App\Host\Folder\FolderModel as Folder;

class FileModel extends Model
{
    protected $table = 'files';

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
