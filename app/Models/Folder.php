<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\File;
use App\Models\User;

class Folder extends Model
{
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
        return $this->hasMany(Folder::class, 'parent_id');
    }

    // A folder can belong to a parent folder
    public function parentFolder()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }
}
