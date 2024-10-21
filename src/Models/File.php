<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Host;
use App\Models\Folder;

class File extends Model
{
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
